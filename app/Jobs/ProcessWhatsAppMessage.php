<?php

namespace App\Jobs;

use App\Models\Otp;
use App\Services\ChatbotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    public int $tries   = 2;
    public int $timeout = 30;

    public function __construct(
        private string $phoneNumber,
        private string $messageId,
        private string $messageText,
    ) {}

    public function handle(ChatbotService $chatbot): void
    {
        // Reply to user first
        $chatbot->handle($this->phoneNumber, $this->messageText);

        // OTP fallback
        if (preg_match('/\b(\d{6})\b/', $this->messageText, $matches)) {
            $this->processOtp($matches[1]);
        }

        // Mark as read last — failure here doesn't affect the user
        $this->markAsRead();
    }

    private function markAsRead(): void
    {
        try {
            Http::withToken(env('GRAPH_API_TOKEN'))
                ->connectTimeout(3)
                ->timeout(5)
                ->post('https://graph.facebook.com/v24.0/' . env('BUSINESS_PHONE_NUMBER_ID') . '/messages', [
                    'messaging_product' => 'whatsapp',
                    'status'            => 'read',
                    'message_id'        => $this->messageId,
                ]);
        } catch (\Throwable) {
            // mark-as-read is best-effort; silently ignore failures
        }
    }

    private function processOtp(string $otpCode): void
    {
        try {
            Otp::verify($this->phoneNumber, $otpCode, 'login');
        } catch (\Throwable $e) {
            Log::error('ProcessWhatsAppMessage: OTP verify failed', ['error' => $e->getMessage()]);
        }
    }
}
