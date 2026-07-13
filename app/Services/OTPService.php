<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OTPService
{
    /**
     * Generate an OTP for the given phone number and store in cache for 5 minutes.
     */
    public function generate(string $phone): string
    {
        $otp = (string) rand(100000, 999999);
        Cache::put("otp_{$phone}", $otp, now()->addMinutes(5));
        Log::info("OTP for {$phone} is {$otp}");

        return $otp;
    }

    /**
     * Verify the given OTP against the stored one.
     */
    public function verify(string $phone, string $otp): bool
    {
        $cachedOtp = Cache::get("otp_{$phone}");
        if ($cachedOtp && $cachedOtp === $otp) {
            Cache::forget("otp_{$phone}");

            return true;
        }

        return false;
    }

    /**
     * Resend an OTP.
     */
    public function resend(string $phone): string
    {
        return $this->generate($phone);
    }
}
