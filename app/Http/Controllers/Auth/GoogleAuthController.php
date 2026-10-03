<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if (! $this->configured()) return back()->with('error', 'O login com Google ainda não está configurado.');
        $state = Str::random(40);
        $request->session()->put('google_login_state', $state);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => $this->clientId(),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function callback(Request $request): RedirectResponse
    {
        $expectedState = (string) $request->session()->pull('google_login_state');
        abort_unless($request->filled('code') && $expectedState !== '' && hash_equals($expectedState, (string) $request->state), 419);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $request->code,
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect_uri' => $this->redirectUri(),
            'grant_type' => 'authorization_code',
        ]);
        if ($response->failed()) return redirect()->route('login')->with('error', 'Não foi possível autenticar com o Google.');

        $profile = Http::withToken($response->json('access_token'))->get('https://www.googleapis.com/oauth2/v3/userinfo');
        if ($profile->failed() || ! $profile->json('sub') || ! $profile->json('email') || ! $profile->json('email_verified')) {
            return redirect()->route('login')->with('error', 'O Google não retornou um perfil válido.');
        }

        $data = $profile->json();
        $user = User::where('google_id', $data['sub'])->first();
        if (! $user) $user = User::where('email', $data['email'])->first();
        if (! $user) {
            $user = User::create([
                'name' => $data['name'] ?? $data['email'],
                'email' => $data['email'],
                'google_id' => $data['sub'],
                'google_avatar' => $data['picture'] ?? null,
                'registered_with_google' => true,
                'password' => Str::random(40),
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
        } else {
            $user->forceFill(['google_id' => $data['sub'], 'google_avatar' => $data['picture'] ?? $user->google_avatar])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function clientId(): ?string { return SystemSetting::getValue('google_client_id', config('services.google.client_id')); }
    private function clientSecret(): ?string { return SystemSetting::getSecret('google_client_secret', config('services.google.client_secret')); }
    private function configured(): bool { return filled($this->clientId()) && filled($this->clientSecret()) && (bool) SystemSetting::getValue('google_login_enabled', false); }
    private function redirectUri(): string { return url('/login/google/callback'); }
}
