<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'personal_info',
        'summary',
        'additional_info',
        'section_order',
    ];

    protected $casts = [
        'personal_info' => 'array',
        'section_order' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class)->orderBy('order_index')->orderBy('id');
    }

    public function education(): HasMany
    {
        return $this->hasMany(Education::class)->orderBy('order_index')->orderBy('id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class)->orderBy('order_index')->orderBy('id');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class)->orderBy('order_index')->orderBy('id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('order_index')->orderBy('id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class)->orderBy('order_index')->orderBy('id');
    }

    /**
     * Duplicate this resume and all its related items.
     */
    public function duplicate(): self
    {
        $newResume = $this->replicate();
        $newResume->title = "{$this->title} (Cópia)";
        $newResume->save();

        foreach ($this->experiences as $item) {
            $clone = $item->replicate();
            $clone->resume_id = $newResume->id;
            $clone->save();
        }

        foreach ($this->education as $item) {
            $clone = $item->replicate();
            $clone->resume_id = $newResume->id;
            $clone->save();
        }

        foreach ($this->courses as $item) {
            $clone = $item->replicate();
            $clone->resume_id = $newResume->id;
            $clone->save();
        }

        foreach ($this->certifications as $item) {
            $clone = $item->replicate();
            $clone->resume_id = $newResume->id;
            $clone->save();
        }

        foreach ($this->skills as $item) {
            $clone = $item->replicate();
            $clone->resume_id = $newResume->id;
            $clone->save();
        }

        foreach ($this->languages as $item) {
            $clone = $item->replicate();
            $clone->resume_id = $newResume->id;
            $clone->save();
        }

        return $newResume;
    }
}
