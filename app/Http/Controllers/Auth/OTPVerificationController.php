<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class OTPVerificationController extends Controller
{
    protected OTPService $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show OTP verification form
     */
    public function show(Request $request): Response
    {
        $phoneNumber = $request->session()->get('auth.phone_number');
        $email = $request->session()->get('auth.email');
        $method = $request->session()->get('auth.otp_method', 'whatsapp');

        if (!$phoneNumber && !$email) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/OTPVerification', [
            'phoneNumber' => $phoneNumber ? $this->maskPhoneNumber($phoneNumber) : null,
            'email' => $email ? $this->maskEmail($email) : null,
            'method' => $method,
            'contact' => $phoneNumber ?: $email,
        ]);
    }

    /**
     * Send OTP via selected method
     */
    public function send(Request $request)
    {
        $request->validate([
            'method' => 'required|in:whatsapp,sms,email',
            'contact' => 'required|string',
        ]);

        $method = $request->input('method');
        $contact = $request->input('contact');
        $type = 'login';

        try {
            if ($method === 'email') {
                $response = $this->otpService->sendEmailOTP($contact, $type);
            } else {
                $response = $this->otpService->sendWhatsAppOTP($contact, $type, $request->ip());

                // Fallback to SMS if WhatsApp fails
                if (!$response['success'] && $method === 'whatsapp') {
                    $response = $this->otpService->sendSmsOTP($contact, $type, $request->ip());
                    $response['method'] = 'sms';
                }
            }

            if ($response['success']) {
                // Store method and contact in session for verification
                $request->session()->put([
                    'auth.otp_method' => $method,
                    'auth.otp_contact' => $contact,
                    'auth.otp_phone_number' => $method !== 'email' ? $contact : null,
                    'auth.otp_email' => $method === 'email' ? $contact : null,
                    'auth.otp_expires_at' => $response['data']['expires_at'] ?? null,
                ]);

                // Check if it's an AJAX/fetch request (for switchMethod)
                if ($request->expectsJson()) {
                    return response()->json($response);
                }

                // For form submissions, redirect to OTP verification page
                return redirect()->route('otp.show')->with('success', 'OTP sent successfully!');
            }

            if ($request->expectsJson()) {
                return response()->json($response, 400);
            }

            return back()->with('error', $response['message'] ?? 'Failed to send OTP. Please try again.');

        } catch (\Exception $e) {
            Log::error('OTP sending failed', [
                'method' => $method,
                'contact' => $contact,
                'error' => $e->getMessage(),
            ]);

            $message = 'Failed to send OTP. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return back()->with('error', $message);
        }
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'contact' => 'required|string',
        ]);

        $code = $request->input('code');
        $contact = $request->input('contact');
        $type = 'login';

        try {
            $otp = null;

            // Try phone verification first
            if (preg_match('/^\+?[\d\s\-()]+$/', $contact)) {
                $otp = Otp::verify($contact, $code, $type);

                if ($otp) {
                    // Phone-based OTP verified
                    $request->session()->put('auth.otp_verified', true);
                    $request->session()->put('auth.verified_contact', $contact);

                    Log::info('OTP verified successfully', [
                        'phone_number' => $contact,
                        'type' => $type,
                    ]);

                    return redirect()->route('dashboard')->with('success', 'OTP verified successfully!');
                }
            } else {
                // Email-based OTP verification
                $otp = Otp::verifyEmail($contact, $code, $type);

                if ($otp) {
                    $request->session()->put('auth.otp_verified', true);
                    $request->session()->put('auth.verified_contact', $contact);

                    Log::info('Email OTP verified successfully', [
                        'email' => $contact,
                        'type' => $type,
                    ]);

                    return redirect()->route('dashboard')->with('success', 'OTP verified successfully!');
                }
            }

            return back()->with('error', 'Invalid or expired OTP code. Please try again.');

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'contact' => $contact,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Verification failed. Please try again.');
        }
    }

    /**
     * Resend OTP to same or different method
     */
    public function resend(Request $request)
    {
        $request->validate([
            'method' => 'required|in:whatsapp,sms,email',
            'contact' => 'required|string',
        ]);

        $method = $request->input('method');
        $contact = $request->input('contact');
        $type = 'login';

        try {
            if ($method === 'email') {
                $response = $this->otpService->sendEmailOTP($contact, $type);
            } else {
                $response = $this->otpService->sendWhatsAppOTP($contact, $type, $request->ip());

                // Fallback to SMS
                if (!$response['success'] && $method === 'whatsapp') {
                    $response = $this->otpService->sendSmsOTP($contact, $type, $request->ip());
                    $response['method'] = 'sms';
                }
            }

            if ($response['success']) {
                // Update session with new method
                $request->session()->put([
                    'auth.otp_method' => $method,
                    'auth.otp_expires_at' => $response['data']['expires_at'] ?? null,
                ]);
            }

            // Always return JSON for resend (AJAX request)
            return response()->json($response, $response['success'] ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('OTP resend failed', [
                'method' => $method,
                'contact' => $contact,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.',
            ], 500);
        }
    }

    /**
     * Mask phone number for display
     */
    private function maskPhoneNumber(string $phoneNumber): string
    {
        // Keep first 3 and last 2 digits visible
        $length = strlen($phoneNumber);
        if ($length > 5) {
            return substr($phoneNumber, 0, 3) . str_repeat('*', $length - 5) . substr($phoneNumber, -2);
        }
        return str_repeat('*', max(0, $length - 2)) . substr($phoneNumber, -2);
    }

    /**
     * Mask email for display
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            $username = $parts[0];
            $domain = $parts[1];

            $visibleChars = max(1, strlen($username) - 2);
            $maskedUsername = substr($username, 0, $visibleChars) . str_repeat('*', strlen($username) - $visibleChars);

            return $maskedUsername . '@' . $domain;
        }
        return $email;
    }
}
