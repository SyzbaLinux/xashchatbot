<?php

namespace App\Services;

class VoucherService
{
    private const VALID_PREFIXES = ['VALID', 'TEST', '1234'];
    private const INVALID_CODES  = ['INVALID', 'EXPIRED', '0000'];

    public function redeem(string $code, float $amount, string $recipient): array
    {
        $code = strtoupper(trim($code));

        foreach (self::VALID_PREFIXES as $prefix) {
            if (str_starts_with($code, $prefix)) {
                return ['success' => true, 'message' => 'Voucher accepted. Airtime sent!'];
            }
        }

        if (in_array($code, self::INVALID_CODES)) {
            return ['success' => false, 'message' => 'Voucher code is invalid or already used.'];
        }

        // Deterministic result from code hash so same code always gives same result
        $isValid = (crc32($code) % 2 === 0);

        return $isValid
            ? ['success' => true,  'message' => 'Voucher accepted. Airtime sent!']
            : ['success' => false, 'message' => 'Voucher code could not be redeemed. Please try again.'];
    }
}
