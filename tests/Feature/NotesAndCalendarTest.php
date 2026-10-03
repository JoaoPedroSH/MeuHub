<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotesAndCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_note_with_new_tags(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('notes.store'), [
            'title' => 'Ideia de projeto', 'content' => 'Explorar uma nova direção.', 'tags' => ['ideias', 'pessoal'],
        ])->assertSessionHas('success');

        $note = Note::with('tags')->first();
        $this->assertSame('Ideia de projeto', $note->title);
        $this->assertCount(2, $note->tags);
        $this->assertDatabaseHas('tags', ['user_id' => $user->id, 'name' => 'ideias']);
    }

    public function test_user_can_create_a_calendar_event_linked_to_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('calendar.store'), [
            'title' => 'Revisar ideia', 'starts_at' => '2026-10-10 10:00', 'note_id' => $note->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('calendar_events', ['user_id' => $user->id, 'note_id' => $note->id, 'title' => 'Revisar ideia']);
    }

    public function test_user_cannot_change_another_users_note_or_event(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $owner->id]);
        $event = CalendarEvent::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->put(route('notes.update', $note), ['title' => 'Invadido'])->assertForbidden();
        $this->actingAs($other)->delete(route('calendar.destroy', $event))->assertForbidden();
    }
}
