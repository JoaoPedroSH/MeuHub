<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'dashboard_shortcuts', 'admin_shortcuts'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'dashboard_shortcuts' => 'array',
            'admin_shortcuts' => 'array',
            'password' => 'hashed',
        ];
    }

    public function resumes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Resume::class)->latest('updated_at');
    }

    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(Note::class); }
    public function tags(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(Tag::class); }
    public function calendarEvents(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(CalendarEvent::class); }
    public function googleCalendarConnection(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(GoogleCalendarConnection::class); }
}
