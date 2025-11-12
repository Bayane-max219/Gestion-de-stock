<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class TwoFactorAuthenticationService
{
    /**
     * Generate a new 2FA code for a user
     */
    public function generateCode(User $user): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store the code in cache for 10 minutes
        Cache::put("2fa_code_{$user->id}", Hash::make($code), now()->addMinutes(10));
        
        return $code;
    }

    /**
     * Verify a 2FA code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $storedCode = Cache::get("2fa_code_{$user->id}");
        
        if (!$storedCode) {
            return false;
        }

        $result = Hash::check($code, $storedCode);
        
        if ($result) {
            Cache::forget("2fa_code_{$user->id}");
            $user->update(['two_factor_confirmed_at' => now()]);
        }

        return $result;
    }

    /**
     * Generate recovery codes for a user
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }

    /**
     * Enable 2FA for a user
     */
    public function enable(User $user): array
    {
        $recoveryCodes = $this->generateRecoveryCodes();
        
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => Str::random(32),
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
            'two_factor_confirmed_at' => null
        ]);

        return $recoveryCodes;
    }

    /**
     * Disable 2FA for a user
     */
    public function disable(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null
        ]);

        Cache::forget("2fa_code_{$user->id}");
    }

    /**
     * Confirm if a recovery code is valid
     */
    public function confirmRecoveryCode(User $user, string $code): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        
        $key = array_search($code, $recoveryCodes);
        
        if ($key !== false) {
            unset($recoveryCodes[$key]);
            $user->update([
                'two_factor_recovery_codes' => json_encode(array_values($recoveryCodes)),
                'two_factor_confirmed_at' => now()
            ]);
            return true;
        }

        return false;
    }
}