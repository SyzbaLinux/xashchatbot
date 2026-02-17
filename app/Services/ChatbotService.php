<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private const MENU_IMAGE_URL = 'https://xashchatbot.emmanuelsiziba.co.zw/xashwelcome.jpg';

    public function __construct(private VoucherService $voucherService) {}

    // -------------------------------------------------------------------------
    // Entry point
    // -------------------------------------------------------------------------

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
            'idle', 'main_menu'      => $this->showMainMenu($session, $input),
            'airtime_recipient'      => $this->collectRecipient($session, $input),
            'airtime_payment'        => $this->collectPaymentMethod($session, $input),
            'airtime_voucher'        => $this->redeemVoucher($session, $input),
            'airtime_amount'         => $this->collectAmount($session, $input),
            'airtime_express_confirm'=> $this->confirmExpress($session, $input),
            default                  => $this->showMainMenu($session, $input),
        };
    }

    // -------------------------------------------------------------------------
    // Step: Main menu
    // -------------------------------------------------------------------------

    private function showMainMenu(ChatSession $session, string $input): ?string
    {
        if ($session->step === 'main_menu') {
            if ($input === '1') {
                $session->advance('airtime_recipient');
                return "Please enter the recipient's phone number (e.g. +263771234567), or type *cancel* to go back:";
            }
            if ($input === '2') {
                $session->reset();
                $this->sendInteractiveButtons(
                    $session->phone_number,
                    "Data bundle purchases are coming soon. Stay tuned! 📶",
                    [['id' => 'home', 'title' => '🏠 Main Menu']]
                );
                return null;
            }
            if ($input === '3') {
                $session->reset();
                $this->sendInteractiveButtons(
                    $session->phone_number,
                    "Utility bill payments are coming soon. Stay tuned! 💡",
                    [['id' => 'home', 'title' => '🏠 Main Menu']]
                );
                return null;
            }
            if ($input === 'home') {
                // interactive button reply for Home
            }
        }

        $session->advance('main_menu');

        $caption = "👋 *Welcome to XASH!*\n"
            . "_Fast & secure mobile payments via WhatsApp._\n\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "💡 *What would you like to do?*\n\n"
            . "1️⃣  Buy Airtime\n"
            . "2️⃣  Buy Data Bundles\n"
            . "3️⃣  Pay Utility Bills\n\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "_Reply with the number of your choice._";

        $this->sendImage($session->phone_number, self::MENU_IMAGE_URL, $caption);
        return null;
    }

    // -------------------------------------------------------------------------
    // Step 1: Collect recipient number
    // -------------------------------------------------------------------------

    private function collectRecipient(ChatSession $session, string $input): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if (!preg_match('/^\+?\d{7,15}$/', $input)) {
            return "❌ That doesn't look like a valid phone number.\n\nPlease enter the recipient's number (e.g. *+263771234567*), or type *cancel*:";
        }

        $session->advance('airtime_payment', ['recipient' => $input]);

        // Show payment method selection immediately after collecting the number
        $this->sendPaymentButtons($session->phone_number);

        return null; // buttons already sent
    }

    // -------------------------------------------------------------------------
    // Payment buttons helper — send interactive buttons for enabled methods
    // -------------------------------------------------------------------------

    private function sendPaymentButtons(string $phone): void
    {
        // WhatsApp interactive messages allow max 3 buttons; reserve one for Cancel
        $methods = PaymentMethod::enabled()->take(2)->get();

        if ($methods->isEmpty()) {
            $this->sendText($phone, "⚠️ No payment methods are currently available. Please contact support.");
            return;
        }

        $buttons = $methods->map(fn($m) => [
            'id'    => $m->code,                      // e.g. "voucher", "express"
            'title' => mb_substr($m->name, 0, 20),    // WhatsApp button title max 20 chars
        ])->toArray();

        $buttons[] = ['id' => 'cancel_payment', 'title' => 'Cancel'];

        $bodyText = "Great! 👍 How would you like to pay?\n\n"
            . "_All purchases are final and non-refundable._";

        $this->sendInteractiveButtons($phone, $bodyText, $buttons);
    }

    // -------------------------------------------------------------------------
    // Step 2: Handle payment method button reply
    // -------------------------------------------------------------------------

    private function collectPaymentMethod(ChatSession $session, string $input): ?string
    {
        $input = strtolower(trim($input));

        if ($input === 'cancel_payment' || $input === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        // Find the chosen payment method
        $method = PaymentMethod::where('code', $input)->where('is_enabled', true)->first();

        if (!$method) {
            // Graceful fallback: re-send the buttons
            $this->sendPaymentButtons($session->phone_number);
            return null;
        }

        if ($method->code === 'voucher') {
            $session->advance('airtime_voucher', ['payment_method' => 'voucher']);
            return "Please enter your voucher code:";
        }

        // For express / EFT / mobile money — collect amount first
        $session->advance('airtime_amount', ['payment_method' => $method->code, 'payment_method_name' => $method->name]);
        $recipient = $session->session_data['recipient'] ?? 'unknown';
        return "Enter the airtime amount you'd like to send to {$recipient} (e.g. 100):";
    }

    // -------------------------------------------------------------------------
    // Step 3a: Redeem voucher code (voucher determines amount)
    // -------------------------------------------------------------------------

    private function redeemVoucher(ChatSession $session, string $input): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        $data      = $session->session_data ?? [];
        $recipient = $data['recipient'] ?? '';

        $result = $this->voucherService->redeem($input, $recipient);
        $status = $result['success'] ? 'success' : 'failed';
        $amount = (float) ($result['amount'] ?? 0);

        Transaction::create([
            'phone_number'   => $session->phone_number,
            'type'           => 'airtime',
            'recipient'      => $recipient,
            'amount'         => $amount,
            'currency'       => 'ZAR',
            'payment_method' => 'voucher',
            'voucher_code'   => strtoupper(trim($input)),
            'status'         => $status,
            'response_data'  => $result,
        ]);

        if ($result['success']) {
            $session->reset();
            $this->sendInteractiveButtons(
                $session->phone_number,
                "✅ {$result['message']}",
                [['id' => 'home', 'title' => '🏠 Main Menu']]
            );
            return null;
        }

        // On failure keep in voucher step so user can retry
        return "❌ {$result['message']}\n\nPlease try a different code, or type *cancel* to go back.";
    }

    // -------------------------------------------------------------------------
    // Step 3b: Collect amount (for express / non-voucher methods)
    // -------------------------------------------------------------------------

    private function collectAmount(ChatSession $session, string $input): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if (!is_numeric($input) || (float) $input <= 0) {
            return "Please enter a valid amount (numbers only, e.g. *200*), or type *cancel*:";
        }

        $data          = $session->session_data ?? [];
        $amount        = (float) $input;
        $recipient     = $data['recipient'] ?? 'unknown';
        $methodName    = $data['payment_method_name'] ?? 'Express';

        $session->advance('airtime_express_confirm', ['amount' => $amount]);

        $summary = "📋 *Order Summary*\n\n"
            . "📱 Recipient: *{$recipient}*\n"
            . "💰 Amount: *ZAR {$amount}*\n"
            . "💳 Payment: *{$methodName}*\n\n"
            . "_All purchases are final and non-refundable._\n\n"
            . "Confirm your payment?";

        $this->sendInteractiveButtons($session->phone_number, $summary, [
            ['id' => 'express_confirm', 'title' => '✅ Confirm & Pay'],
            ['id' => 'express_cancel',  'title' => '❌ Cancel'],
        ]);

        return null;
    }

    // -------------------------------------------------------------------------
    // Step 4: Confirm express payment
    // -------------------------------------------------------------------------

    private function confirmExpress(ChatSession $session, string $input): ?string
    {
        $input = strtolower(trim($input));

        if ($input === 'express_cancel' || $input === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if ($input === 'express_confirm' || $input === 'confirm') {
            $data      = $session->session_data ?? [];
            $recipient = $data['recipient'] ?? '';
            $amount    = (float) ($data['amount'] ?? 0);
            $method    = $data['payment_method'] ?? 'express';

            // Log the transaction as pending (real processing not yet implemented)
            Transaction::create([
                'phone_number'   => $session->phone_number,
                'type'           => 'airtime',
                'recipient'      => $recipient,
                'amount'         => $amount,
                'currency'       => 'ZAR',
                'payment_method' => $method,
                'status'         => 'pending',
                'response_data'  => ['note' => 'Express payment — awaiting integration'],
            ]);

            $session->reset();

            $this->sendInteractiveButtons(
                $session->phone_number,
                "⏳ Your order has been received!\n\n"
                    . "ZAR {$amount} airtime for {$recipient} is being processed. "
                    . "You will receive a confirmation shortly.\n\n"
                    . "_Express payment integration coming soon._",
                [['id' => 'home', 'title' => '🏠 Main Menu']]
            );

            return null;
        }

        // Unrecognised input — re-prompt with buttons
        $data      = $session->session_data ?? [];
        $amount    = $data['amount'] ?? 0;
        $recipient = $data['recipient'] ?? 'unknown';

        $this->sendInteractiveButtons($session->phone_number,
            "Please confirm your payment of ZAR {$amount} to {$recipient}:",
            [
                ['id' => 'express_confirm', 'title' => '✅ Confirm & Pay'],
                ['id' => 'express_cancel',  'title' => '❌ Cancel'],
            ]
        );

        return null;
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
                        'reply' => [
                            'id'    => $btn['id'],
                            'title' => mb_substr($btn['title'], 0, 20), // WhatsApp limit
                        ],
                    ], $buttons),
                ],
            ],
        ]);
    }

    private function post(string $phone, array $body): void
    {
        try {
            $response = Http::withToken(config('whatsapp.graph_api_token'))
                ->post(
                    'https://graph.facebook.com/v24.0/' . config('whatsapp.phone_number_id') . '/messages',
                    array_merge(['messaging_product' => 'whatsapp', 'to' => $phone], $body)
                );

            if (!$response->successful()) {
                Log::warning('ChatbotService: send failed', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ChatbotService: send exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
