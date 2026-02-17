<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    /**
     * Verify webhook token (GET from Meta)
     */
    public function verify(Request $request): Response
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('whatsapp.webhook_verify_token')) {
            return response($challenge, 200);
        }

        Log::warning('WhatsApp webhook verification failed');

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming webhook (POST request from Meta)
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();

        try {
            $entry   = $payload['entry'][0]   ?? null;
            $changes = $entry['changes'][0]    ?? null;
            $value   = $changes['value']       ?? null;

            // Status updates (delivered, read, failed) — acknowledge and move on
            if (isset($value['statuses'])) {
                return response('OK', 200);
            }

            $message = $value['messages'][0] ?? null;

            if (!$message) {
                return response('OK', 200);
            }

            $phone     = $message['from'];
            $messageId = $message['id'];
            $type      = $message['type'];

            $this->markRead($messageId);

            $text = match ($type) {
                'text'        => $message['text']['body'] ?? null,
                'interactive' => $this->extractInteractiveReply($message['interactive'] ?? []),
                default       => null,
            };

            if ($text !== null) {
                Log::info('WhatsApp message received', [
                    'phone' => $phone,
                    'type'  => $type,
                    'text'  => $text,
                ]);

                $this->chatbot->handle($phone, $text);
            }

        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response('OK', 200);
    }

    /**
     * Extract the user-visible reply text from an interactive message.
     * Supports button_reply and list_reply (the two Meta interactive subtypes).
     */
    private function extractInteractiveReply(array $interactive): ?string
    {
        $subtype = $interactive['type'] ?? null;

        return match ($subtype) {
            'button_reply' => $interactive['button_reply']['title'] ?? $interactive['button_reply']['id'] ?? null,
            'list_reply'   => $interactive['list_reply']['title']   ?? $interactive['list_reply']['id']   ?? null,
            default        => null,
        };
    }

    /**
     * Mark a message as read via the Cloud API.
     */
    private function markRead(string $messageId): void
    {
        try {
            Http::withToken(config('whatsapp.graph_api_token'))
                ->post('https://graph.facebook.com/v24.0/' . config('whatsapp.phone_number_id') . '/messages', [
                    'messaging_product' => 'whatsapp',
                    'status'            => 'read',
                    'message_id'        => $messageId,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Could not mark message as read', ['error' => $e->getMessage()]);
        }
    }
}
