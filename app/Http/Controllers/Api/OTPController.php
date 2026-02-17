<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OTPController extends Controller
{
    protected OTPService $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP via selected method (API)
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
                    'auth.otp_expires_at' => $response['data']['expires_at'] ?? null,
                ]);

                return response()->json($response);
            }

            return response()->json($response, 400);

        } catch (\Exception $e) {
            Log::error('OTP sending failed', [
                'method' => $method,
                'contact' => $contact,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify OTP code (API)
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

                    return response()->json([
                        'success' => true,
                        'message' => 'OTP verified successfully',
                        'redirect' => route('dashboard'),
                    ]);
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

                    return response()->json([
                        'success' => true,
                        'message' => 'OTP verified successfully',
                        'redirect' => route('dashboard'),
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP code. Please try again.',
            ], 400);

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'contact' => $contact,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Resend OTP to same or different method (API)
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
}
