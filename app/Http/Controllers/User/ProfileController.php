<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the user profile edit page.
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        $twoFactorEnabled = $user->two_factor_secret !== null;

        return Inertia::render('User/Profile/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('M d, Y'),
            ],
            'twoFactorEnabled' => $twoFactorEnabled,
            'hasRecoveryCodes' => $user->two_factor_recovery_codes !== null,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::user()->id),
            ],
        ]);

        Auth::user()->update($validated);

        return back()->with('flash', [
            'success' => 'Profile updated successfully!',
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^a-zA-Z0-9]/',
                'confirmed',
            ],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('flash', [
            'success' => 'Password updated successfully!',
        ]);
    }

    /**
     * Enable two-factor authentication.
     */
    public function enableTwoFactor(Request $request)
    {
        $user = Auth::user();

        if ($user->two_factor_secret) {
            return back()->with('flash', [
                'warning' => 'Two-factor authentication is already enabled.',
            ]);
        }

        // Generate a new 2FA secret
        $secret = \PragmaRX\Google2FA\Google2FA::generateSecretKey();
        $user->update(['two_factor_secret' => $secret]);

        // Generate recovery codes
        $recoveryCodes = collect(range(1, 8))->map(function () {
            return \Illuminate\Support\Str::random(10);
        })->toArray();

        $user->update(['two_factor_recovery_codes' => json_encode($recoveryCodes)]);

        return Inertia::render('User/Profile/Verify2FA', [
            'secret' => $secret,
            'qrCode' => \PragmaRX\Google2FA\Google2FA::getQRCodeInline(
                config('app.name'),
                $user->email,
                $secret
            ),
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Verify and confirm two-factor authentication setup.
     */
    public function confirmTwoFactor(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        $google2fa = new \PragmaRX\Google2FA\Google2FA();

        if (!$google2fa->verifyKey($user->two_factor_secret, $validated['code'])) {
            return back()->with('flash', [
                'error' => 'Invalid verification code. Please try again.',
            ])->withViewData('viewData', ['invalidCode' => true]);
        }

        // 2FA is now confirmed
        return redirect()->route('user.profile.edit')->with('flash', [
            'success' => 'Two-factor authentication has been enabled successfully!',
        ]);
    }

    /**
     * Disable two-factor authentication.
     */
    public function disableTwoFactor(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return back()->with('flash', [
            'success' => 'Two-factor authentication has been disabled.',
        ]);
    }

    /**
     * Get recovery codes.
     */
    public function getRecoveryCodes(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        if (!$user->two_factor_secret) {
            return back()->with('flash', [
                'error' => 'Two-factor authentication is not enabled.',
            ]);
        }

        // Regenerate recovery codes
        $recoveryCodes = collect(range(1, 8))->map(function () {
            return \Illuminate\Support\Str::random(10);
        })->toArray();

        $user->update(['two_factor_recovery_codes' => json_encode($recoveryCodes)]);

        return back()->with('flash', [
            'success' => 'New recovery codes have been generated.',
        ])->with('recoveryCodes', $recoveryCodes);
    }
}
