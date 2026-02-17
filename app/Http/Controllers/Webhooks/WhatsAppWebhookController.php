<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verify webhook token — GET /webhook
     */
    public function verify(Request $request)
    {
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($token === env('WEBHOOK_VERIFY_TOKEN', 'your_verify_token')) {
            return response($challenge, 200);
        }

        return response('Invalid token', 403);
    }

    /**
     * Receive incoming webhook — POST /webhook
     * Returns 200 immediately; all processing happens in a queued job.
     */
    public function handle(Request $request)
    {
        try {
            $data = $request->json()->all();

            // Ignore status updates (sent, delivered, read, failed) — nothing to process
            if (isset($data['entry'][0]['changes'][0]['value']['statuses'])) {
                return response('OK', 200);
            }

            $message = $data['entry'][0]['changes'][0]['value']['messages'][0] ?? null;

            if (!$message) {
                return response('OK', 200);
            }

            $phone     = $message['from'];
            $messageId = $message['id'];
            $type      = $message['type'];

            Log::info('WhatsApp message received', [
                'phone_number' => $phone,
                'message_id'   => $messageId,
                'type'         => $type,
            ]);

            // Extract the user input depending on message type
            $input = match ($type) {
                'text'        => $message['text']['body'] ?? null,
                'interactive' => $this->extractInteractiveInput($message),
                default       => null,
            };

            if ($input !== null) {
                // Dispatch to queue — webhook returns 200 without waiting
                ProcessWhatsAppMessage::dispatch($phone, $messageId, $input);
            }

        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook error', ['error' => $e->getMessage()]);
        }

        // Always return 200 immediately so WhatsApp does not retry
        return response('OK', 200);
    }

    private function extractInteractiveInput(array $message): ?string
    {
        $type = $message['interactive']['type'] ?? null;

        return match ($type) {
            'button_reply' => $message['interactive']['button_reply']['id'] ?? null,
            'list_reply'   => $message['interactive']['list_reply']['id']   ?? null,
            default        => null,
        };
    }
}
