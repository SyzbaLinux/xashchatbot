<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    /**
     * Send OTP via WhatsApp
     */
    public function sendWhatsAppOTP(string $phoneNumber, string $type = 'login', ?string $ipAddress = null): array
    {
        // Rate limiting check
        if (!$this->canSendOTP($phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please wait before requesting again.',
                'retry_after' => $this->getRetryAfter($phoneNumber),
            ];
        }

        // Create OTP record
        $otp = Otp::createForPhone($phoneNumber, $type, $ipAddress, 'whatsapp');

        // Send WhatsApp message
        $sent = $this->deliverWhatsAppMessage($phoneNumber, $otp->otp_code);

        if ($sent) {
            $this->incrementOTPAttempts($phoneNumber);

            Log::info('WhatsApp OTP sent successfully', [
                'phone_number' => $phoneNumber,
                'type' => $type,
                'otp_id' => $otp->id,
            ]);

            return [
                'success' => true,
                'message' => 'OTP sent to WhatsApp',
                'data' => [
                    'phone_number' => $phoneNumber,
                    'method' => 'whatsapp',
                    'expires_at' => $otp->expires_at->toIso8601String(),
                    'expires_in_seconds' => $otp->getTimeRemaining(),
                ],
            ];
        }

        Log::warning('Failed to send WhatsApp OTP', [
            'phone_number' => $phoneNumber,
            'type' => $type,
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send OTP via WhatsApp. Please try SMS instead.',
        ];
    }

    /**
     * Send OTP via SMS
     */
    public function sendSmsOTP(string $phoneNumber, string $type = 'login', ?string $ipAddress = null): array
    {
        // Rate limiting check
        if (!$this->canSendOTP($phoneNumber)) {
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please wait before requesting again.',
                'retry_after' => $this->getRetryAfter($phoneNumber),
            ];
        }

        // Create OTP record
        $otp = Otp::createForPhone($phoneNumber, $type, $ipAddress, 'sms');

        // Send SMS
        $sent = $this->deliverSmsMessage($phoneNumber, $otp->otp_code);

        if ($sent) {
            $this->incrementOTPAttempts($phoneNumber);

            Log::info('SMS OTP sent successfully', [
                'phone_number' => $phoneNumber,
                'type' => $type,
                'otp_id' => $otp->id,
            ]);

            return [
                'success' => true,
                'message' => 'OTP sent via SMS',
                'data' => [
                    'phone_number' => $phoneNumber,
                    'method' => 'sms',
                    'expires_at' => $otp->expires_at->toIso8601String(),
                    'expires_in_seconds' => $otp->getTimeRemaining(),
                ],
            ];
        }

        Log::warning('Failed to send SMS OTP', [
            'phone_number' => $phoneNumber,
            'type' => $type,
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send OTP via SMS. Please try again.',
        ];
    }

    /**
     * Send OTP via Email
     */
    public function sendEmailOTP(string $email, string $type = 'registration', ?string $ipAddress = null): array
    {
        // Rate limiting check
        if (!$this->canSendEmailOTP($email)) {
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please wait before requesting again.',
                'retry_after' => $this->getEmailRetryAfter($email),
            ];
        }

        // Create OTP record
        $otp = Otp::createForEmail($email, $type, $ipAddress);

        // Send Email
        $sent = $this->deliverEmailMessage($email, $otp->otp_code);

        if ($sent) {
            $this->incrementEmailOTPAttempts($email);

            Log::info('Email OTP sent successfully', [
                'email' => $email,
                'type' => $type,
                'otp_id' => $otp->id,
            ]);

            // Log to console in debug
            if (config('app.debug')) {
                echo "\n═══════════════════════════════════════\n";
                echo "🔐 EMAIL OTP: {$otp->otp_code}\n";
                echo "📧 Email: {$email}\n";
                echo "⏰ Expires: {$otp->expires_at->toDateTimeString()}\n";
                echo "═══════════════════════════════════════\n\n";
            }

            return [
                'success' => true,
                'message' => 'OTP sent to your email',
                'data' => [
                    'email' => $email,
                    'method' => 'email',
                    'expires_at' => $otp->expires_at->toIso8601String(),
                    'expires_in_seconds' => $otp->getTimeRemaining(),
                ],
            ];
        }

        Log::warning('Failed to send Email OTP', [
            'email' => $email,
            'type' => $type,
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send OTP via Email. Please try again.',
        ];
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP(string $phoneNumber, string $code, string $type = 'login'): array
    {
        $otp = Otp::verify($phoneNumber, $code, $type);

        if ($otp) {
            $this->clearOTPAttempts($phoneNumber);

            Log::info('OTP verified successfully', [
                'phone_number' => $phoneNumber,
                'type' => $type,
                'otp_id' => $otp->id,
            ]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully',
                'otp' => $otp,
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid or expired OTP code. Please try again.',
        ];
    }

    /**
     * Verify Email OTP code
     */
    public function verifyEmailOTP(string $email, string $code, string $type = 'registration'): array
    {
        $otp = Otp::verifyEmail($email, $code, $type);

        if ($otp) {
            $this->clearEmailOTPAttempts($email);

            Log::info('Email OTP verified successfully', [
                'email' => $email,
                'type' => $type,
                'otp_id' => $otp->id,
            ]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully',
                'otp' => $otp,
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid or expired OTP code. Please try again.',
        ];
    }

    /**
     * Resend OTP
     */
    public function resendOTP(string $phoneNumber, string $method = 'whatsapp', string $type = 'login'): array
    {
        if ($method === 'whatsapp') {
            return $this->sendWhatsAppOTP($phoneNumber, $type);
        } else {
            return $this->sendSmsOTP($phoneNumber, $type);
        }
    }

    /**
     * Deliver WhatsApp message
     */
    private function deliverWhatsAppMessage(string $phoneNumber, string $otpCode): bool
    {
        try {
            $message = "🔐 *Your ErrandRunner Verification Code*\n\n";
            $message .= "Your OTP is: *{$otpCode}*\n\n";
            $message .= "⏰ This code will expire in 10 minutes.\n";
            $message .= "⚠️ Never share this code with anyone.\n\n";
            $message .= "If you didn't request this code, please ignore this message.";

            $response = Http::withToken(env('GRAPH_API_TOKEN'))
                ->post('https://graph.facebook.com/v22.0/' . env('BUSINESS_PHONE_NUMBER_ID') . '/messages', [
                    'messaging_product' => 'whatsapp',
                    'to' => $phoneNumber,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message delivered', [
                    'phone_number' => $phoneNumber,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::warning('WhatsApp message failed', [
                'phone_number' => $phoneNumber,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp delivery error', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deliver SMS message
     */
    private function deliverSmsMessage(string $phoneNumber, string $otpCode): bool
    {
        try {
            // Using Twilio (configure in .env)
            if (!env('TWILIO_SID') || !env('TWILIO_TOKEN')) {
                Log::warning('Twilio not configured');
                return false;
            }

            $message = "Your ErrandRunner verification code is: {$otpCode}. This code will expire in 10 minutes. Never share this code with anyone.";

            $response = Http::withBasicAuth(env('TWILIO_SID'), env('TWILIO_TOKEN'))
                ->post("https://api.twilio.com/2010-04-01/Accounts/" . env('TWILIO_SID') . "/Messages.json", [
                    'From' => env('TWILIO_FROM_NUMBER'),
                    'To' => $phoneNumber,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::info('SMS delivered successfully', [
                    'phone_number' => $phoneNumber,
                ]);
                return true;
            }

            Log::warning('SMS delivery failed', [
                'phone_number' => $phoneNumber,
                'status' => $response->status(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('SMS delivery error', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deliver Email message
     */
    private function deliverEmailMessage(string $email, string $otpCode): bool
    {
        try {
            Mail::send('emails.otp', ['otp' => $otpCode], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your ErrandRunner Verification Code');
            });

            Log::info('Email delivered successfully', [
                'email' => $email,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Email delivery error', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if can send OTP (rate limiting)
     */
    private function canSendOTP(string $phoneNumber): bool
    {
        $attempts = Cache::get($this->getRateLimitKey($phoneNumber), 0);
        return $attempts < 5; // Max 5 attempts per 10 minutes
    }

    /**
     * Check if can send email OTP (rate limiting)
     */
    private function canSendEmailOTP(string $email): bool
    {
        $attempts = Cache::get($this->getEmailRateLimitKey($email), 0);
        return $attempts < 3; // Max 3 attempts per 10 minutes
    }

    /**
     * Increment OTP attempts
     */
    private function incrementOTPAttempts(string $phoneNumber): void
    {
        $key = $this->getRateLimitKey($phoneNumber);
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, now()->addMinutes(10));
    }

    /**
     * Increment email OTP attempts
     */
    private function incrementEmailOTPAttempts(string $email): void
    {
        $key = $this->getEmailRateLimitKey($email);
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, now()->addMinutes(10));
    }

    /**
     * Clear OTP attempts
     */
    private function clearOTPAttempts(string $phoneNumber): void
    {
        Cache::forget($this->getRateLimitKey($phoneNumber));
    }

    /**
     * Clear email OTP attempts
     */
    private function clearEmailOTPAttempts(string $email): void
    {
        Cache::forget($this->getEmailRateLimitKey($email));
    }

    /**
     * Get retry after seconds
     */
    private function getRetryAfter(string $phoneNumber): int
    {
        $key = $this->getRateLimitKey($phoneNumber);
        $attempts = Cache::get($key, 0);
        return (6 - $attempts) * 120; // Incremental backoff
    }

    /**
     * Get email retry after seconds
     */
    private function getEmailRetryAfter(string $email): int
    {
        $key = $this->getEmailRateLimitKey($email);
        $attempts = Cache::get($key, 0);
        return (4 - $attempts) * 120; // Incremental backoff
    }

    /**
     * Get rate limit key
     */
    private function getRateLimitKey(string $phoneNumber): string
    {
        return 'otp_attempts_' . md5($phoneNumber);
    }

    /**
     * Get email rate limit key
     */
    private function getEmailRateLimitKey(string $email): string
    {
        return 'email_otp_attempts_' . md5($email);
    }
}
