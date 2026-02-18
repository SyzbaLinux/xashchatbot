<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\VoucherProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private const MENU_IMAGE_URL = 'https://xashchatbot.emmanuelsiziba.co.zw/xashwelcome.jpg';

    // Methods that require a mobile phone number + PIN flow
    private const MOBILE_MONEY_METHODS = ['ecocash', 'onemoney', 'innbucks', 'mpesa', 'orangemoney'];

    public function __construct(private VoucherService $voucherService) {}

    // -------------------------------------------------------------------------
    // Entry point
    // -------------------------------------------------------------------------

    private const GREETINGS = ['hi', 'hie', 'hello', 'hey', 'start', 'menu', 'home'];

    public function handle(string $phone, string $input): void
    {
        $input   = trim($input);
        $session = ChatSession::getOrCreate($phone);

        // Any greeting resets the session and shows the main menu fresh
        if (in_array(strtolower($input), self::GREETINGS)) {
            $session->reset();
        }

        $reply = $this->route($session, $input);

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
            'idle', 'main_menu'          => $this->showMainMenu($session, $input),

            // Airtime steps
            'airtime_recipient'          => $this->collectRecipient($session, $input, 'airtime'),
            'airtime_payment_type'       => $this->collectPaymentType($session, $input, 'airtime'),
            'airtime_voucher_provider'   => $this->collectVoucherProvider($session, $input, 'airtime'),
            'airtime_express_method'     => $this->collectExpressMethod($session, $input, 'airtime'),
            'airtime_mobile_phone'       => $this->collectMobilePhone($session, $input, 'airtime'),
            'airtime_currency'           => $this->collectCurrency($session, $input, 'airtime'),
            'airtime_voucher'            => $this->redeemVoucher($session, $input),
            'airtime_amount'             => $this->collectAmount($session, $input),
            'airtime_express_confirm'    => $this->confirmExpress($session, $input),

            // Data bundle steps
            'bundles_recipient'          => $this->collectRecipient($session, $input, 'bundles'),
            'bundles_payment_type'       => $this->collectPaymentType($session, $input, 'bundles'),
            'bundles_voucher_provider'   => $this->collectVoucherProvider($session, $input, 'bundles'),
            'bundles_express_method'     => $this->collectExpressMethod($session, $input, 'bundles'),
            'bundles_mobile_phone'       => $this->collectMobilePhone($session, $input, 'bundles'),
            'bundles_currency'           => $this->collectCurrency($session, $input, 'bundles'),
            'bundles_voucher'            => $this->redeemVoucher($session, $input),
            'bundles_amount'             => $this->collectAmount($session, $input),
            'bundles_express_confirm'    => $this->confirmExpress($session, $input),

            default                      => $this->showMainMenu($session, $input),
        };
    }

    // -------------------------------------------------------------------------
    // Step: Main menu
    // -------------------------------------------------------------------------

    private function showMainMenu(ChatSession $session, string $input): ?string
    {
        if ($session->step === 'main_menu') {
            if ($input === '1') {
                $session->advance('airtime_recipient', ['service' => 'airtime']);
                return "Please enter the recipient's phone number (e.g. +263771234567), or type *cancel* to go back:";
            }
            if ($input === '2') {
                $session->advance('bundles_recipient', ['service' => 'bundles']);
                return "Please enter the recipient's phone number (e.g. +263771234567), or type *cancel* to go back:";
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

    private function collectRecipient(ChatSession $session, string $input, string $service): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if (!preg_match('/^\+?\d{7,15}$/', $input)) {
            return "❌ That doesn't look like a valid phone number.\n\nPlease enter the recipient's number (e.g. *+263771234567*), or type *cancel*:";
        }

        $session->advance("{$service}_payment_type", ['recipient' => $input]);
        $this->sendPaymentTypeButtons($session->phone_number);

        return null;
    }

    // -------------------------------------------------------------------------
    // Payment type buttons: Vouchers / Express
    // -------------------------------------------------------------------------

    private function sendPaymentTypeButtons(string $phone): void
    {
        // Build dynamic hints from DB so they always reflect what's actually available
        $voucherHint = VoucherProvider::enabled()
            ->orderBy('sort_order')
            ->limit(3)
            ->pluck('name')
            ->join(', ');

        $expressHint = PaymentMethod::enabled()
            ->where('code', '!=', 'voucher')
            ->orderBy('sort_order')
            ->limit(3)
            ->pluck('name')
            ->join(', ');

        $body = "💳 *How would you like to pay?*\n\n"
            . "🎟 *Vouchers* — {$voucherHint}\n"
            . "⚡ *Express* — {$expressHint}";

        $this->sendInteractiveButtons($phone, $body, [
            ['id' => 'pay_voucher',    'title' => 'Vouchers'],
            ['id' => 'pay_express',    'title' => 'Express'],
            ['id' => 'cancel_payment', 'title' => 'Cancel'],
        ]);
    }

    // -------------------------------------------------------------------------
    // Step 2: Handle Vouchers / Express selection
    // -------------------------------------------------------------------------

    private function collectPaymentType(ChatSession $session, string $input, string $service): ?string
    {
        $input = strtolower(trim($input));

        if ($input === 'cancel_payment' || $input === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if ($input === 'pay_voucher') {
            $providers = VoucherProvider::enabled()->get();

            if ($providers->isEmpty()) {
                $session->reset();
                return "⚠️ No voucher providers are currently available. Please contact support.";
            }

            $session->advance("{$service}_voucher_provider");

            $rows = $providers->map(fn($p) => [
                'id'    => 'vp_' . $p->code,
                'title' => $p->name,
            ])->toArray();

            $this->sendInteractiveList(
                $session->phone_number,
                "Select your voucher provider:",
                "Choose Provider",
                "Voucher Providers",
                $rows
            );
            return null;
        }

        if ($input === 'pay_express') {
            $methods = PaymentMethod::enabled()->where('code', '!=', 'voucher')->get();

            if ($methods->isEmpty()) {
                $session->reset();
                return "⚠️ No express payment methods are currently available. Please contact support.";
            }

            $session->advance("{$service}_express_method");

            $rows = $methods->map(fn($m) => [
                'id'    => 'em_' . $m->code,
                'title' => $m->name,
            ])->toArray();

            $this->sendInteractiveList(
                $session->phone_number,
                "Select your payment method:",
                "Choose Method",
                "Payment Methods",
                $rows
            );
            return null;
        }

        // Unrecognised input — re-send buttons
        $this->sendPaymentTypeButtons($session->phone_number);
        return null;
    }

    // -------------------------------------------------------------------------
    // Step 3a: Voucher provider selection
    // -------------------------------------------------------------------------

    private function collectVoucherProvider(ChatSession $session, string $input, string $service): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        // Strip the "vp_" prefix added to list row IDs
        $code     = str_starts_with($input, 'vp_') ? substr($input, 3) : $input;
        $provider = VoucherProvider::where('code', $code)->where('is_enabled', true)->first();

        if (!$provider) {
            $providers = VoucherProvider::enabled()->get();
            $rows = $providers->map(fn($p) => [
                'id'    => 'vp_' . $p->code,
                'title' => $p->name,
            ])->toArray();
            $this->sendInteractiveList(
                $session->phone_number,
                "Please select a valid voucher provider:",
                "Choose Provider",
                "Voucher Providers",
                $rows
            );
            return null;
        }

        $session->advance("{$service}_voucher", [
            'payment_method'        => 'voucher',
            'voucher_provider'      => $provider->code,
            'voucher_provider_name' => $provider->name,
        ]);

        return "Please enter your *{$provider->name}* voucher code:";
    }

    // -------------------------------------------------------------------------
    // Step 3b: Express method selection
    // -------------------------------------------------------------------------

    private function collectExpressMethod(ChatSession $session, string $input, string $service): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        // Strip the "em_" prefix added to list row IDs
        $code   = str_starts_with($input, 'em_') ? substr($input, 3) : $input;
        $method = PaymentMethod::where('code', $code)
            ->where('is_enabled', true)
            ->where('code', '!=', 'voucher')
            ->first();

        if (!$method) {
            $methods = PaymentMethod::enabled()->where('code', '!=', 'voucher')->get();
            $rows = $methods->map(fn($m) => [
                'id'    => 'em_' . $m->code,
                'title' => $m->name,
            ])->toArray();
            $this->sendInteractiveList(
                $session->phone_number,
                "Please select a valid payment method:",
                "Choose Method",
                "Payment Methods",
                $rows
            );
            return null;
        }

        $data      = $session->session_data ?? [];
        $recipient = $data['recipient'] ?? 'unknown';

        // Mobile money (EcoCash, OneMoney, etc.) → collect payer's phone number first
        if (in_array($method->code, self::MOBILE_MONEY_METHODS)) {
            $session->advance("{$service}_mobile_phone", [
                'payment_method'      => $method->code,
                'payment_method_name' => $method->name,
            ]);
            return "📱 Enter your *{$method->name}* mobile number (e.g. +263771234567), or type *cancel*:";
        }

        // Card / EFT → select currency first
        $session->advance("{$service}_currency", [
            'payment_method'      => $method->code,
            'payment_method_name' => $method->name,
        ]);
        $this->sendCurrencyButtons($session->phone_number);
        return null;
    }

    // -------------------------------------------------------------------------
    // Step 3c: Collect mobile money phone number
    // -------------------------------------------------------------------------

    private function collectMobilePhone(ChatSession $session, string $input, string $service): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if (!preg_match('/^\+?\d{7,15}$/', $input)) {
            return "❌ That doesn't look like a valid mobile number.\n\nPlease enter your number (e.g. *+263771234567*), or type *cancel*:";
        }

        $data      = $session->session_data ?? [];
        $recipient = $data['recipient'] ?? 'unknown';

        $session->advance("{$service}_currency", ['mobile_phone' => $input]);
        $this->sendCurrencyButtons($session->phone_number);
        return null;
    }

    // -------------------------------------------------------------------------
    // Currency buttons helper
    // -------------------------------------------------------------------------

    private function sendCurrencyButtons(string $phone): void
    {
        $this->sendInteractiveButtons($phone,
            "💱 *Select currency for this transaction:*",
            [
                ['id' => 'cur_USD', 'title' => '🇺🇸 USD – US Dollar'],
                ['id' => 'cur_ZAR', 'title' => '🇿🇦 ZAR – S.A. Rand'],
                ['id' => 'cur_BWP', 'title' => '🇧🇼 BWP – Botswana Pula'],
            ]
        );
    }

    // -------------------------------------------------------------------------
    // Step 3d: Collect currency
    // -------------------------------------------------------------------------

    private function collectCurrency(ChatSession $session, string $input, string $service): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        $map = [
            'cur_usd' => ['code' => 'USD', 'symbol' => '$',   'name' => 'US Dollar'],
            'cur_zar' => ['code' => 'ZAR', 'symbol' => 'R',   'name' => 'S.A. Rand'],
            'cur_bwp' => ['code' => 'BWP', 'symbol' => 'P',   'name' => 'Botswana Pula'],
        ];

        $chosen = $map[strtolower(trim($input))] ?? null;

        if (!$chosen) {
            $this->sendCurrencyButtons($session->phone_number);
            return null;
        }

        $data      = $session->session_data ?? [];
        $recipient = $data['recipient'] ?? 'unknown';

        $session->advance("{$service}_amount", [
            'currency'        => $chosen['code'],
            'currency_symbol' => $chosen['symbol'],
            'currency_name'   => $chosen['name'],
        ]);

        return "Enter the amount in *{$chosen['code']}* to send to *{$recipient}* (e.g. 100), or type *cancel*:";
    }

    // -------------------------------------------------------------------------
    // Step 4a: Redeem voucher code
    // -------------------------------------------------------------------------

    private function redeemVoucher(ChatSession $session, string $input): ?string
    {
        if (strtolower($input) === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        $data      = $session->session_data ?? [];
        $recipient = $data['recipient'] ?? '';
        $service   = $data['service'] ?? 'airtime';

        $result = $this->voucherService->redeem($input, $recipient);
        $status = $result['success'] ? 'success' : 'failed';
        $amount = (float) ($result['amount'] ?? 0);

        Transaction::create([
            'phone_number'   => $session->phone_number,
            'type'           => $service,
            'recipient'      => $recipient,
            'amount'         => $amount,
            'currency'       => 'ZAR',
            'payment_method' => 'voucher',
            'voucher_code'   => strtoupper(trim($input)),
            'status'         => $status,
            'response_data'  => array_merge($result, ['provider' => $data['voucher_provider'] ?? null]),
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

        return "❌ {$result['message']}\n\nPlease try a different code, or type *cancel* to go back.";
    }

    // -------------------------------------------------------------------------
    // Step 4b: Collect amount (for express methods)
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

        $data           = $session->session_data ?? [];
        $amount         = (float) $input;
        $recipient      = $data['recipient']      ?? 'unknown';
        $methodName     = $data['payment_method_name'] ?? 'Express';
        $service        = $data['service']        ?? 'airtime';
        $currency       = $data['currency']       ?? 'USD';
        $currencySymbol = $data['currency_symbol'] ?? '$';

        $session->advance("{$service}_express_confirm", ['amount' => $amount]);

        $serviceLabel = $service === 'bundles' ? 'Data Bundle' : 'Airtime';

        $summary = "📋 *Order Summary*\n\n"
            . "📱 Recipient: *{$recipient}*\n"
            . "💰 Amount: *{$currency} {$currencySymbol}{$amount}*\n"
            . "📦 Type: *{$serviceLabel}*\n"
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
    // Step 5: Confirm express payment
    // -------------------------------------------------------------------------

    private function confirmExpress(ChatSession $session, string $input): ?string
    {
        $input = strtolower(trim($input));

        if ($input === 'express_cancel' || $input === 'cancel') {
            $session->reset();
            return $this->showMainMenu($session, '');
        }

        if ($input === 'express_confirm' || $input === 'confirm') {
            $data           = $session->session_data ?? [];
            $recipient      = $data['recipient']           ?? '';
            $amount         = (float) ($data['amount']     ?? 0);
            $method         = $data['payment_method']      ?? 'express';
            $methodName     = $data['payment_method_name'] ?? 'Express';
            $mobilePhone    = $data['mobile_phone']        ?? '';
            $service        = $data['service']             ?? 'airtime';
            $currency       = $data['currency']            ?? 'USD';
            $currencySymbol = $data['currency_symbol']     ?? '$';

            // Mobile money → send USSD push, PIN entered on handset (not in chat)
            if (in_array($method, self::MOBILE_MONEY_METHODS)) {
                $txId = 'SIM-' . strtoupper(substr(md5(uniqid()), 0, 8));

                Transaction::create([
                    'phone_number'   => $session->phone_number,
                    'type'           => $service,
                    'recipient'      => $recipient,
                    'amount'         => $amount,
                    'currency'       => $currency,
                    'payment_method' => $method,
                    'status'         => 'success',
                    'response_data'  => [
                        'note'         => 'Simulated mobile money payment',
                        'mobile_phone' => $mobilePhone,
                        'tx_id'        => $txId,
                    ],
                ]);

                $session->reset();

                $serviceLabel = $service === 'bundles' ? 'data bundle' : 'airtime';

                $this->sendInteractiveButtons(
                    $session->phone_number,
                    "📲 *{$methodName} USSD Request Sent!*\n\n"
                        . "A PIN prompt has been sent to *{$mobilePhone}*.\n"
                        . "Please enter your PIN on your phone when the USSD popup appears.\n\n"
                        . "✅ *{$currency} {$currencySymbol}{$amount}* {$serviceLabel} will be sent to *{$recipient}* once confirmed.\n\n"
                        . "_Transaction ID: {$txId}_",
                    [['id' => 'home', 'title' => '🏠 Main Menu']]
                );

                return null;
            }

            // Card / EFT → pending (awaiting real integration)
            Transaction::create([
                'phone_number'   => $session->phone_number,
                'type'           => $service,
                'recipient'      => $recipient,
                'amount'         => $amount,
                'currency'       => $currency,
                'payment_method' => $method,
                'status'         => 'pending',
                'response_data'  => ['note' => 'EFT/Card payment — awaiting integration'],
            ]);

            $session->reset();

            $this->sendInteractiveButtons(
                $session->phone_number,
                "⏳ Your order has been received!\n\n"
                    . "{$currency} {$currencySymbol}{$amount} for {$recipient} is being processed. "
                    . "You will receive a confirmation shortly.\n\n"
                    . "_Payment integration coming soon._",
                [['id' => 'home', 'title' => '🏠 Main Menu']]
            );

            return null;
        }

        // Unrecognised input — re-prompt
        $data           = $session->session_data ?? [];
        $amount         = $data['amount']          ?? 0;
        $recipient      = $data['recipient']        ?? 'unknown';
        $currency       = $data['currency']         ?? 'USD';
        $currencySymbol = $data['currency_symbol']  ?? '$';

        $this->sendInteractiveButtons($session->phone_number,
            "Please confirm your payment of {$currency} {$currencySymbol}{$amount} to {$recipient}:",
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
                            'title' => mb_substr($btn['title'], 0, 20),
                        ],
                    ], $buttons),
                ],
            ],
        ]);
    }

    private function sendInteractiveList(
        string $phone,
        string $bodyText,
        string $buttonLabel,
        string $sectionTitle,
        array  $rows
    ): void {
        $this->post($phone, [
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $bodyText],
                'action' => [
                    'button'   => mb_substr($buttonLabel, 0, 20),
                    'sections' => [
                        [
                            'title' => mb_substr($sectionTitle, 0, 24),
                            'rows'  => array_map(fn($row) => [
                                'id'    => mb_substr((string) $row['id'], 0, 200),
                                'title' => mb_substr((string) $row['title'], 0, 24),
                            ], $rows),
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function post(string $phone, array $body): void
    {
        try {
            $response = Http::withToken(config('whatsapp.graph_api_token'))
                ->connectTimeout(5)
                ->timeout(10)
                ->post(
                    'https://graph.facebook.com/v24.0/' . config('whatsapp.phone_number_id') . '/messages',
                    array_merge(['messaging_product' => 'whatsapp', 'to' => $phone], $body)
                );

            if (!$response->successful()) {
                Log::error('ChatbotService: send failed', [
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
