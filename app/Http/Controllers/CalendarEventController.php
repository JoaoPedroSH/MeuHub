<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarEventController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        return Inertia::render('Calendar/Index', [
            'events' => $user->calendarEvents()->with('note:id,title')->orderBy('starts_at')->get(),
            'notes' => $user->notes()->select('id', 'title')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        if (!empty($data['note_id'])) abort_unless(auth()->user()->notes()->whereKey($data['note_id'])->exists(), 422);
        auth()->user()->calendarEvents()->create($data);
        return back()->with('success', 'Evento adicionado à agenda.');
    }

    public function update(Request $request, CalendarEvent $calendarEvent): RedirectResponse
    {
        abort_unless($calendarEvent->user_id === auth()->id(), 403);
        $data = $this->validated($request);
        if (!empty($data['note_id'])) abort_unless(auth()->user()->notes()->whereKey($data['note_id'])->exists(), 422);
        $calendarEvent->update($data);
        return back()->with('success', 'Evento atualizado.');
    }

    public function destroy(CalendarEvent $calendarEvent): RedirectResponse
    {
        abort_unless($calendarEvent->user_id === auth()->id(), 403);
        $calendarEvent->delete();
        return back()->with('success', 'Evento removido da agenda.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'all_day' => ['boolean'], 'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'location' => ['nullable', 'string', 'max:255'], 'note_id' => ['nullable', 'integer'],
        ]);
    }
}
