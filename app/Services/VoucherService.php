<?php

namespace App\Services;

class VoucherService
{
    /** Voucher codes with these prefixes always succeed */
    private const VALID_PREFIXES = ['VALID', 'TEST', '1234'];

    /** These exact codes always fail */
    private const INVALID_CODES = ['INVALID', 'EXPIRED', '0000'];

    /** Possible face values a voucher can carry */
    private const FACE_VALUES = [50, 100, 150, 200, 500];

    /**
     * Attempt to redeem a voucher code.
     * Returns ['success', 'message', 'amount'] — amount is 0 on failure.
     */
    public function redeem(string $code, string $recipient): array
    {
        $code = strtoupper(trim($code));

        foreach (self::VALID_PREFIXES as $prefix) {
            if (str_starts_with($code, $prefix)) {
                $amount = $this->faceValue($code);
                return [
                    'success' => true,
                    'message' => "Voucher accepted! {$amount} airtime sent to {$recipient}.",
                    'amount'  => $amount,
                ];
            }
        }

        if (in_array($code, self::INVALID_CODES)) {
            return ['success' => false, 'message' => 'Voucher code is invalid or already used.', 'amount' => 0];
        }

        // Deterministic result from code hash — same code always gives the same outcome
        if (crc32($code) % 2 === 0) {
            $amount = $this->faceValue($code);
            return [
                'success' => true,
                'message' => "Voucher accepted! {$amount} airtime sent to {$recipient}.",
                'amount'  => $amount,
            ];
        }

        return ['success' => false, 'message' => 'Voucher code could not be redeemed. Please try a different code.', 'amount' => 0];
    }

    /** Pick a deterministic face value from the code hash */
    private function faceValue(string $code): int
    {
        return self::FACE_VALUES[abs(crc32($code)) % count(self::FACE_VALUES)];
    }
}
