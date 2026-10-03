<?php

namespace App\Http\Controllers;

use App\Models\GoogleCalendarConnection;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IntegrationController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('profile.edit', ['tab' => 'integracoes']);
    }

    public function connect(Request $request): RedirectResponse
    {
        abort_unless($this->isConfigured(), 503, 'A integração Google ainda não está disponível.');
        $request->session()->put('google_oauth_state', Str::random(40));

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => $this->clientId(),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/calendar.events https://www.googleapis.com/auth/userinfo.email',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $request->session()->get('google_oauth_state'),
        ]));
    }

    public function callback(Request $request): RedirectResponse
    {
        abort_unless($request->filled('code') && hash_equals((string) $request->session()->pull('google_oauth_state'), (string) $request->state), 419);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $request->code, 'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(), 'redirect_uri' => $this->redirectUri(), 'grant_type' => 'authorization_code',
        ]);
        if ($response->failed()) return redirect()->route('profile.edit', ['tab' => 'integracoes'])->with('error', 'Não foi possível conectar ao Google Agenda.');

        $tokens = $response->json();
        $profile = Http::withToken($tokens['access_token'])->get('https://www.googleapis.com/oauth2/v2/userinfo')->json();
        $connection = auth()->user()->googleCalendarConnection;
        auth()->user()->googleCalendarConnection()->updateOrCreate([], [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'] ?? $connection?->refresh_token,
            'token_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
            'google_email' => $profile['email'] ?? null,
            'calendar_id' => $connection?->calendar_id ?? 'primary',
        ]);

        return redirect()->route('profile.edit', ['tab' => 'integracoes'])->with('success', 'Google Agenda conectado.');
    }

    public function sync(): RedirectResponse
    {
        $connection = auth()->user()->googleCalendarConnection;
        if (! $connection) return back()->with('error', 'Conecte o Google Agenda antes de sincronizar.');

        $response = $this->google($connection)->get('https://www.googleapis.com/calendar/v3/calendars/'.rawurlencode($connection->calendar_id).'/events', [
            'singleEvents' => 'true', 'orderBy' => 'startTime', 'timeMin' => now()->subDays(30)->toRfc3339String(), 'timeMax' => now()->addYear()->toRfc3339String(),
        ]);
        if ($response->failed()) return back()->with('error', 'Não foi possível sincronizar o Google Agenda.');

        foreach ($response->json('items', []) as $item) {
            if (($item['status'] ?? null) === 'cancelled' || empty($item['id'])) continue;
            $start = $item['start']['dateTime'] ?? $item['start']['date'] ?? null;
            $end = $item['end']['dateTime'] ?? $item['end']['date'] ?? null;
            if (! $start) continue;
            auth()->user()->calendarEvents()->updateOrCreate(['google_event_id' => $item['id']], [
                'title' => $item['summary'] ?? 'Evento sem título', 'description' => $item['description'] ?? null,
                'starts_at' => Carbon::parse($start), 'ends_at' => $end ? Carbon::parse($end) : null,
                'all_day' => isset($item['start']['date']), 'color' => '#6366f1', 'location' => $item['location'] ?? null, 'source' => 'google',
            ]);
        }

        $connection->touch();
        return back()->with('success', 'Agenda sincronizada com o Google.');
    }

    public function disconnect(): RedirectResponse
    {
        auth()->user()->googleCalendarConnection?->delete();
        auth()->user()->calendarEvents()->where('source', 'google')->delete();
        return back()->with('success', 'Google Agenda desconectado.');
    }

    private function google(GoogleCalendarConnection $connection): PendingRequest
    {
        if ($connection->token_expires_at?->isPast() && $connection->refresh_token) {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => $this->clientId(), 'client_secret' => $this->clientSecret(),
                'refresh_token' => $connection->refresh_token, 'grant_type' => 'refresh_token',
            ]);
            if ($response->successful()) {
                $connection->update(['access_token' => $response->json('access_token'), 'token_expires_at' => now()->addSeconds($response->json('expires_in', 3600))]);
            }
        }
        return Http::withToken($connection->fresh()->access_token)->acceptJson();
    }

    private function clientId(): ?string { return SystemSetting::getValue('google_client_id', config('services.google.client_id')); }
    private function clientSecret(): ?string { return SystemSetting::getSecret('google_client_secret', config('services.google.client_secret')); }
    private function isConfigured(): bool { return filled($this->clientId()) && filled($this->clientSecret()) && (bool) SystemSetting::getValue('google_calendar_enabled', false); }
    private function redirectUri(): string { $redirect = SystemSetting::getValue('google_redirect_uri', config('services.google.redirect')); return Str::startsWith($redirect, 'http') ? $redirect : url($redirect); }
}
