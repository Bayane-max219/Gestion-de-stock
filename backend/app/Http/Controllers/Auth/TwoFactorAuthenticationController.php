<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthenticationController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthenticationService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            throw ValidationException::withMessages([
                'two_factor' => ['Two-factor authentication is already enabled.']
            ]);
        }

        $request->validate([
            'phone' => ['required', 'string', 'max:20']
        ]);

        $user->update(['phone' => $request->phone]);
        
        $recoveryCodes = $this->twoFactorService->enable($user);
        $code = $this->twoFactorService->generateCode($user);

        // Here you would send the code via SMS to the user's phone
        // For development, we'll just return it in the response
        return response()->json([
            'message' => 'Two-factor authentication has been enabled.',
            'verification_code' => $code, // Remove this in production
            'recovery_codes' => $recoveryCodes
        ]);
    }

    public function disable(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_enabled) {
            throw ValidationException::withMessages([
                'two_factor' => ['Two-factor authentication is not enabled.']
            ]);
        }

        $this->twoFactorService->disable($user);

        return response()->json([
            'message' => 'Two-factor authentication has been disabled.'
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6']
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            throw ValidationException::withMessages([
                'two_factor' => ['Two-factor authentication is not enabled.']
            ]);
        }

        if (!$this->twoFactorService->verifyCode($user, $request->code)) {
            throw ValidationException::withMessages([
                'code' => ['The verification code is invalid.']
            ]);
        }

        return response()->json([
            'message' => 'Two-factor authentication verified successfully.'
        ]);
    }

    public function recovery(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:10']
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            throw ValidationException::withMessages([
                'two_factor' => ['Two-factor authentication is not enabled.']
            ]);
        }

        if (!$this->twoFactorService->confirmRecoveryCode($user, $request->code)) {
            throw ValidationException::withMessages([
                'code' => ['The recovery code is invalid.']
            ]);
        }

        return response()->json([
            'message' => 'Two-factor authentication verified successfully using recovery code.'
        ]);
    }

    public function generateNewCode(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_enabled) {
            throw ValidationException::withMessages([
                'two_factor' => ['Two-factor authentication is not enabled.']
            ]);
        }

        $code = $this->twoFactorService->generateCode($user);

        // Here you would send the code via SMS to the user's phone
        // For development, we'll just return it in the response
        return response()->json([
            'message' => 'A new verification code has been generated.',
            'verification_code' => $code // Remove this in production
        ]);
    }
}