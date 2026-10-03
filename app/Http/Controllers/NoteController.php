<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\CalendarEvent;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        return Inertia::render('Notes/Index', [
            'notes' => $user->notes()->with('tags')->latest('updated_at')->get()->map(fn ($note) => array_merge($note->toArray(), [
                'display_tags' => $note->tag_snapshots ?: $note->tags->map(fn ($tag) => ['name' => $tag->name, 'color' => $tag->color])->values(),
            ])),
            'tags' => $user->tags()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:40'],
            'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'all_day' => ['boolean'], 'calendar_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
        $note = auth()->user()->notes()->create(collect($data)->except('tags')->all());
        $this->syncTags($note, $data['tags'] ?? []);
        $this->syncCalendarEvent($note);
        return back()->with('success', 'Anotação criada com sucesso.');
    }

    public function update(Request $request, Note $note): RedirectResponse
    {
        abort_unless($note->user_id === auth()->id(), 403);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'], 'content' => ['nullable', 'string'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'], 'tags' => ['nullable', 'array'], 'tags.*' => ['string', 'max:40'],
            'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'all_day' => ['boolean'], 'calendar_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
        $note->update(collect($data)->except('tags')->all());
        $this->syncTags($note, $data['tags'] ?? []);
        $this->syncCalendarEvent($note);
        return back()->with('success', 'Anotação atualizada.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        abort_unless($note->user_id === auth()->id(), 403);
        auth()->user()->calendarEvents()->where('note_id', $note->id)->where('source', 'local')->delete();
        $note->delete();
        return back()->with('success', 'Anotação excluída.');
    }

    private function syncTags(Note $note, array $names): void
    {
        $tags = collect($names)->map(fn ($name) => trim($name))->filter()->unique()->map(fn ($name) =>
            auth()->user()->tags()->firstOrCreate(['name' => $name], ['color' => $this->tagColor($name)])
        );
        $ids = $tags->pluck('id');
        $note->tags()->sync($ids);
        $note->update(['tag_snapshots' => $tags->map(fn ($tag) => ['name' => $tag->name, 'color' => $tag->color])->values()->all()]);
    }

    public function destroyTag(Tag $tag): RedirectResponse
    {
        abort_unless($tag->user_id === auth()->id(), 403);
        $tag->delete();
        return back()->with('success', 'Tag removida do catálogo. As notas existentes continuam preservadas.');
    }

    private function syncCalendarEvent(Note $note): void
    {
        $event = auth()->user()->calendarEvents()->where('note_id', $note->id)->where('source', 'local')->first();
        if (! $note->starts_at) { $event?->delete(); return; }
        auth()->user()->calendarEvents()->updateOrCreate(
            ['note_id' => $note->id, 'source' => 'local'],
            ['title' => $note->title, 'description' => $note->content, 'starts_at' => $note->starts_at, 'ends_at' => $note->ends_at, 'all_day' => $note->all_day, 'color' => $note->calendar_color ?: '#6366f1']
        );
    }

    private function tagColor(string $name): string
    {
        $colors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6'];
        return $colors[abs(crc32(mb_strtolower($name))) % count($colors)];
    }
}
