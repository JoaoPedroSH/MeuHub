<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\SystemSetting;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'tab' => $request->query('tab', 'perfil'),
            'google' => [
                'configured' => filled(SystemSetting::getValue('google_client_id', config('services.google.client_id'))) && filled(SystemSetting::getSecret('google_client_secret', config('services.google.client_secret'))),
                'enabled' => (bool) SystemSetting::getValue('google_calendar_enabled', false),
                'connected' => (bool) $request->user()->googleCalendarConnection,
                'email' => $request->user()->googleCalendarConnection?->google_email,
                'last_synced_at' => $request->user()->googleCalendarConnection?->updated_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function shortcuts(Request $request): RedirectResponse
    {
        $validated = $request->validate(['shortcuts' => ['nullable', 'array'], 'shortcuts.*' => ['in:resumes,notes,calendar,profile']]);
        $allowed = $request->user()->is_admin ? ['notes', 'calendar', 'profile'] : ['resumes', 'notes', 'calendar', 'profile'];
        $selected = $validated['shortcuts'] ?? [];
        $request->user()->update(['dashboard_shortcuts' => array_values(array_intersect(array_unique($selected), $allowed))]);
        return Redirect::route('dashboard')->with('success', 'Atalhos do dashboard atualizados.');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
