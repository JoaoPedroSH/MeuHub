<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::count(),
                'resumes' => \App\Models\Resume::count(),
                'notes' => \App\Models\Note::count(),
                'events' => \App\Models\CalendarEvent::count(),
            ],
            'google' => $this->googleStatus(),
            'shortcuts' => $this->adminShortcuts(),
        ]);
    }

    public function shortcuts(Request $request): RedirectResponse
    {
        $validated = $request->validate(['shortcuts' => ['nullable', 'array'], 'shortcuts.*' => ['in:admin-users,admin-integrations']]);
        $request->user()->update(['admin_shortcuts' => array_values(array_unique($validated['shortcuts'] ?? []))]);
        return redirect()->route('admin.dashboard')->with('success', 'Atalhos administrativos atualizados.');
    }

    public function users(): Response
    {
        return Inertia::render('Admin/Users', ['users' => User::withCount(['resumes', 'notes', 'calendarEvents'])->latest()->get()]);
    }

    public function integrations(): Response
    {
        return Inertia::render('Admin/Integrations', ['google' => $this->googleStatus()]);
    }

    public function updateGoogleCredentials(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'string', 'max:255'],
            'client_secret' => ['nullable', 'string', 'max:500'],
            'redirect_uris' => ['nullable', 'array'],
            'redirect_uris.*' => ['nullable', 'url', 'max:500'],
        ]);
        SystemSetting::setValue('google_client_id', $data['client_id']);
        SystemSetting::setSecret('google_client_secret', $data['client_secret'] ?? null);
        $redirectUris = array_values(array_filter($data['redirect_uris'] ?? []));
        if ($redirectUris) {
            SystemSetting::setList('google_redirect_uris', $redirectUris);
            SystemSetting::setValue('google_redirect_uri', $redirectUris[0]);
        }
        return back()->with('success', 'Credenciais e URLs do Google salvas com segurança.');
    }

    public function toggleGoogle(Request $request): RedirectResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        SystemSetting::setValue('google_calendar_enabled', $data['enabled']);
        return back()->with('success', $data['enabled'] ? 'Integração Google Agenda ativada.' : 'Integração Google Agenda desativada.');
    }

    public function toggleGoogleLogin(Request $request): RedirectResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        SystemSetting::setValue('google_login_enabled', $data['enabled']);
        return back()->with('success', $data['enabled'] ? 'Login com Google ativado.' : 'Login com Google desativado.');
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'Você não pode alterar o próprio acesso administrativo.');
        $user->update(['is_admin' => ! $user->is_admin]);
        return back()->with('success', $user->is_admin ? 'Acesso administrativo concedido.' : 'Acesso administrativo removido.');
    }

    private function googleStatus(): array
    {
        $clientId = SystemSetting::getValue('google_client_id', config('services.google.client_id'));
        $clientSecret = SystemSetting::getSecret('google_client_secret', config('services.google.client_secret'));
        return [
            'configured' => filled($clientId) && filled($clientSecret),
            'client_id' => $clientId,
            'has_secret' => filled($clientSecret),
            'redirect_uri' => $this->redirectUri(),
            'redirect_uris' => SystemSetting::getList('google_redirect_uris', [$this->redirectUri(), url('/login/google/callback')]),
            'enabled' => (bool) SystemSetting::getValue('google_calendar_enabled', false),
            'login_enabled' => (bool) SystemSetting::getValue('google_login_enabled', false),
            'google_registered_users' => User::where('registered_with_google', true)->count(),
            'connected_users' => User::has('googleCalendarConnection')->count(),
        ];
    }

    private function adminShortcuts(): array
    {
        return $this->userShortcuts(auth()->user()->admin_shortcuts, ['admin-users', 'admin-integrations']);
    }

    private function userShortcuts(?array $selected, array $defaults): array
    {
        return $selected === null ? $defaults : array_values(array_intersect($selected, $defaults));
    }

    private function redirectUri(): string
    {
        $redirect = SystemSetting::getValue('google_redirect_uri', config('services.google.redirect'));
        return Str::startsWith($redirect, 'http') ? $redirect : url($redirect);
    }
}
