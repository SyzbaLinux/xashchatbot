<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private const MENU_IMAGE_URL = 'https://img.freepik.com/premium-photo/beautiful-senior-woman-using-mobile-phone-park_216356-1859.jpg';

    public function __construct(private VoucherService $voucherService) {}

    /**
     * Entry point: resolve session, route input, send reply.
     * A null reply means the step already handled its own send (e.g. image+caption).
     */
    public function handle(string $phone, string $input): void
    {
        $session = ChatSession::getOrCreate($phone);
        $reply   = $this->route($session, trim($input));

        if ($reply !== null) {
            $this->sendText($phone, $reply);
        }
    }

    // -------------------------------------------------------------------------
    // Router
    // -------------------------------------------------------------------------

    private function route(ChatSession $session, string $input): ?string
    {
        return match ($session->step) {
            'idle', 'main_menu' => $this->showMainMenu($session, $input),
            'airtime_recipient'  => $this->collectRecipient($session, $input),
            'airtime_amount'     => $this->collectAmount($session, $input),
            'airtime_payment'    => $this->collectPayment($session, $input),
            'airtime_voucher'    => $this->redeemVoucher($session, $input),
            default              => $this->showMainMenu($session, $input),
        };
    }

    // -------------------------------------------------------------------------
    // Steps
    // -------------------------------------------------------------------------

    /**
     * Show the main menu as an image message with caption.
     * Returns null because the send is handled here directly.
     */
    private function showMainMenu(ChatSession $session, string $input): ?string
    {
        if ($session->step === 'main_menu') {
            switch ($input) {
                case '1':
                    $session->advance('airtime_recipient');
                    return "Please enter the recipient's phone number (e.g. +263771234567):";

                case '2':
                    $session->advance('idle');
                    $this->sendInteractiveButtons(
                        $session->phone_number,
                        "Data bundle purchases are coming soon. Stay tuned!",
                        [['id' => 'home', 'title' => '🏠 Home']]
                    );
                    return null;

                case '3':
                    $session->advance('idle');
                    $this->sendInteractiveButtons(
                        $session->phone_number,
                        "Utility bill payments are coming soon. Stay tuned!",
                        [['id' => 'home', 'title' => '🏠 Home']]
                    );
                    return null;
            }
        }

        $session->advance('main_menu');

        $caption = "👋 *Welcome to Xash!*\n"
            . "_Your fast & easy mobile payments assistant._\n\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "💡 *What would you like to do?*\n\n"
            . "1️⃣  Buy Airtime\n"
            . "2️⃣  Buy Data Bundles\n"
            . "3️⃣  Pay Utility Bills\n\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "_Simply reply with the number of your choice to get started._";

        $this->sendImage($session->phone_number, self::MENU_IMAGE_URL, $caption);

        return null; // image+caption already sent
    }

    private function collectRecipient(ChatSession $session, string $input): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if (!preg_match('/^\+?\d{7,15}$/', $input)) {
            return "That doesn't look like a valid phone number. Please enter the recipient's number (e.g. +263771234567), or type *cancel* to go back:";
        }

        $session->advance('airtime_amount', ['recipient' => $input]);

        return "Enter the amount you'd like to recharge (e.g. 200):";
    }

    private function collectAmount(ChatSession $session, string $input): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if (!is_numeric($input) || (float) $input <= 0) {
            return "Please enter a valid amount (numbers only, e.g. 200), or type *cancel* to go back:";
        }

        $amount    = (float) $input;
        $recipient = $session->session_data['recipient'] ?? 'unknown';

        $methods    = PaymentMethod::enabled()->get();
        $methodList = '';
        $methodMap  = [];
        $i          = 1;

        foreach ($methods as $method) {
            $methodList      .= "{$i}. {$method->name}\n";
            $methodMap[$i]    = $method->code;
            $i++;
        }

        $session->advance('airtime_payment', ['amount' => $amount, 'payment_method_map' => $methodMap]);

        $cancelNumber = $i;

        return "You're about to buy \${$amount} airtime for {$recipient}.\n"
            . "All purchases are final and non-refundable.\n\n"
            . "How would you like to pay?\n"
            . $methodList
            . "{$cancelNumber}. Cancel";
    }

    private function collectPayment(ChatSession $session, string $input): ?string
    {
        $data         = $session->session_data ?? [];
        $methodMap    = $data['payment_method_map'] ?? [];
        $cancelNumber = count($methodMap) + 1;

        if (strtolower($input) === 'cancel' || $input === (string) $cancelNumber) {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        $choice = (int) $input;

        if (!isset($methodMap[$choice])) {
            $list = '';
            foreach ($methodMap as $num => $code) {
                $method = PaymentMethod::where('code', $code)->first();
                $list  .= "{$num}. " . ($method?->name ?? $code) . "\n";
            }
            $list .= "{$cancelNumber}. Cancel";

            return "Invalid choice. Please select a valid option:\n\n" . $list;
        }

        $chosenCode = $methodMap[$choice];

        if ($chosenCode === 'voucher') {
            $session->advance('airtime_voucher', ['payment_method' => $chosenCode]);
            return "Please enter your voucher code:";
        }

        $session->advance('idle', ['payment_method' => $chosenCode]);
        $method = PaymentMethod::where('code', $chosenCode)->first();

        $this->sendInteractiveButtons(
            $session->phone_number,
            "Payment via {$method?->name} is not yet available. Stay tuned!",
            [['id' => 'home', 'title' => '🏠 Home']]
        );
        return null;
    }

    private function redeemVoucher(ChatSession $session, string $input): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        $data      = $session->session_data ?? [];
        $recipient = $data['recipient']      ?? '';
        $amount    = (float) ($data['amount'] ?? 0);

        $result = $this->voucherService->redeem($input, $amount, $recipient);
        $status = $result['success'] ? 'success' : 'failed';

        Transaction::create([
            'phone_number'   => $session->phone_number,
            'type'           => 'airtime',
            'recipient'      => $recipient,
            'amount'         => $amount,
            'currency'       => 'USD',
            'payment_method' => 'voucher',
            'voucher_code'   => strtoupper(trim($input)),
            'status'         => $status,
            'response_data'  => $result,
        ]);

        if ($result['success']) {
            $session->reset();
            $this->sendInteractiveButtons(
                $session->phone_number,
                "✓ Voucher accepted! Airtime of \${$amount} has been sent to {$recipient}.",
                [['id' => 'home', 'title' => '🏠 Home']]
            );
            return null;
        }

        // On failure keep the user in the voucher step so they can retry
        $session->advance('airtime_voucher', $data);

        return "✗ {$result['message']}\nPlease try a different code, or type *cancel* to go back.";
    }

    // -------------------------------------------------------------------------
    // WhatsApp senders
    // -------------------------------------------------------------------------

    private function sendText(string $phone, string $message): void
    {
        $this->post($phone, [
            'type' => 'text',
            'text' => ['body' => $message],
        ]);
    }

    /**
     * Send an interactive button message.
     * $buttons: array of ['id' => string, 'title' => string] (max 3).
     */
    private function sendInteractiveButtons(string $phone, string $bodyText, array $buttons): void
    {
        $this->post($phone, [
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => $bodyText],
                'action' => [
                    'buttons' => array_map(fn($btn) => [
                        'type'  => 'reply',
                        'reply' => ['id' => $btn['id'], 'title' => $btn['title']],
                    ], $buttons),
                ],
            ],
        ]);
    }

    private function sendImage(string $phone, string $url, string $caption): void
    {
        $this->post($phone, [
            'type'  => 'image',
            'image' => [
                'link'    => $url,
                'caption' => $caption,
            ],
        ]);
    }

    private function post(string $phone, array $body): void
    {
        try {
            $response = Http::withToken(config('whatsapp.graph_api_token'))
                ->post('https://graph.facebook.com/v24.0/' . config('whatsapp.phone_number_id') . '/messages',
                    array_merge(['messaging_product' => 'whatsapp', 'to' => $phone], $body)
                );

            if (!$response->successful()) {
                Log::warning('ChatbotService: WhatsApp send failed', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ChatbotService: WhatsApp send exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
