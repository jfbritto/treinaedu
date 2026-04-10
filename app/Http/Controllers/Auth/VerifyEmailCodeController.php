<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifyEmailCodeController extends Controller
{
    public function show(Request $request)
    {
        $email = $request->session()->get('verification_email');

        if (!$email) {
            return redirect()->route('register');
        }

        return view('auth.verify-code', compact('email'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = $request->session()->get('verification_email');

        if (!$email) {
            return redirect()->route('register');
        }

        $record = DB::table('email_verification_codes')
            ->where('email', $email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Código inválido ou expirado. Tente novamente.']);
        }

        // Mark email as verified
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('register');
        }

        $user->update(['email_verified_at' => now()]);

        // Clean up codes
        DB::table('email_verification_codes')->where('email', $email)->delete();

        // Clear session
        $request->session()->forget('verification_email');

        // Login and redirect
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'E-mail verificado com sucesso! Bem-vindo ao TreinaEdu.');
    }

    public function resend(Request $request)
    {
        $email = $request->session()->get('verification_email');

        if (!$email) {
            return redirect()->route('register');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('register');
        }

        $this->sendCode($user);

        return back()->with('status', 'Novo código enviado para seu e-mail.');
    }

    public static function sendCode(User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Remove old codes
        DB::table('email_verification_codes')->where('email', $user->email)->delete();

        // Store new code
        DB::table('email_verification_codes')->insert([
            'email' => $user->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->notify(new EmailVerificationCodeNotification($code));
    }
}
