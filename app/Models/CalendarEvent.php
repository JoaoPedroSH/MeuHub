<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'note_id', 'title', 'description', 'starts_at', 'ends_at', 'all_day', 'color', 'location', 'source', 'google_event_id'];
    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'all_day' => 'boolean'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function note(): BelongsTo { return $this->belongsTo(Note::class); }
}
