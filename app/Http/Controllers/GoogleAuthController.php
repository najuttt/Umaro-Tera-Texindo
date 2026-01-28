<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartMigrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        session([
            'guest_session_id' => session()->getId(),
            'login_redirect' => route('checkout.page'),
            'login_reason'   => 'payment',
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'name'      => $googleUser->name,
                'google_id' => $googleUser->id,
                'password'  => bcrypt(Str::random(32)),
                'role'      => User::where('email', $googleUser->email)->first()->role ?? 'user',
            ]
        );

        Auth::login($user, true);

        // ✅ MIGRATE CART GUEST KE USER (TAMBAHKAN INI)
        CartMigrationService::migrateGuestCartToUser($user->id);

        session(['checkout_redirect_after_logout' => route('checkout.page')]);

        // ⬅️ LANGSUNG KE CHECKOUT
        return redirect()->route('checkout.page')
            ->with('open_payment', true);
    }
}