<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Otp extends Model
{
    protected $table = 'otps';

    protected $fillable = [
        'phone_number',
        'email',
        'otp_code',
        'type',
        'method',
        'verified',
        'ip_address',
        'attempts',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Create OTP for phone number
     */
    public static function createForPhone(string $phoneNumber, string $type = 'login', ?string $ipAddress = null, string $method = 'whatsapp'): self
    {
        // Invalidate previous OTPs
        self::where('phone_number', $phoneNumber)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->update(['expires_at' => now()]);

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'phone_number' => $phoneNumber,
            'otp_code' => $otp,
            'type' => $type,
            'method' => $method,
            'ip_address' => $ipAddress,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    /**
     * Create OTP for email
     */
    public static function createForEmail(string $email, string $type = 'registration', ?string $ipAddress = null): self
    {
        // Invalidate previous OTPs
        self::where('email', $email)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->update(['expires_at' => now()]);

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'email' => $email,
            'otp_code' => $otp,
            'type' => $type,
            'method' => 'email',
            'ip_address' => $ipAddress,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    /**
     * Verify OTP code for phone number
     */
    public static function verify(string $phoneNumber, string $code, string $type = 'login'): ?self
    {
        $otp = self::where('phone_number', $phoneNumber)
            ->where('type', $type)
            ->where('otp_code', $code)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->first();

        if ($otp) {
            // Check attempts
            if ($otp->attempts >= 3) {
                return null; // Too many attempts
            }

            // Mark as verified
            $otp->update([
                'verified' => true,
                'verified_at' => now(),
            ]);

            return $otp;
        }

        // Increment attempts on the latest OTP for this phone
        $latestOtp = self::where('phone_number', $phoneNumber)
            ->where('type', $type)
            ->latest()
            ->first();

        if ($latestOtp) {
            $latestOtp->increment('attempts');
        }

        return null;
    }

    /**
     * Verify OTP code for email
     */
    public static function verifyEmail(string $email, string $code, string $type = 'registration'): ?self
    {
        $otp = self::where('email', $email)
            ->where('type', $type)
            ->where('otp_code', $code)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->first();

        if ($otp) {
            // Check attempts
            if ($otp->attempts >= 3) {
                return null; // Too many attempts
            }

            // Mark as verified
            $otp->update([
                'verified' => true,
                'verified_at' => now(),
            ]);

            return $otp;
        }

        // Increment attempts on the latest OTP for this email
        $latestOtp = self::where('email', $email)
            ->where('type', $type)
            ->latest()
            ->first();

        if ($latestOtp) {
            $latestOtp->increment('attempts');
        }

        return null;
    }

    /**
     * Get the most recent valid OTP for a phone number
     */
    public static function getLatestForPhone(string $phoneNumber, string $type = 'login'): ?self
    {
        return self::where('phone_number', $phoneNumber)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Get the most recent valid OTP for an email
     */
    public static function getLatestForEmail(string $email, string $type = 'registration'): ?self
    {
        return self::where('email', $email)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Check if OTP is still valid
     */
    public function isValid(): bool
    {
        return !$this->verified && $this->expires_at > now() && $this->attempts < 3;
    }

    /**
     * Get time remaining in seconds
     */
    public function getTimeRemaining(): int
    {
        return max(0, $this->expires_at->diffInSeconds(now()));
    }

    /**
     * Get formatted expiry time
     */
    public function getFormattedExpiry(): string
    {
        $seconds = $this->getTimeRemaining();
        $minutes = intdiv($seconds, 60);
        $secs = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $secs);
    }
}
