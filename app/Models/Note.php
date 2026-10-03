<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'content', 'color', 'tag_snapshots', 'starts_at', 'ends_at', 'all_day', 'calendar_color'];
    protected $casts = ['tag_snapshots' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'all_day' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class); }
}
