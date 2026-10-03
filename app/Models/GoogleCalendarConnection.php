<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleCalendarConnection extends Model
{
    protected $fillable = ['user_id', 'access_token', 'refresh_token', 'token_expires_at', 'google_email', 'calendar_id'];
    protected $hidden = ['access_token', 'refresh_token'];
    protected $casts = ['access_token' => 'encrypted', 'refresh_token' => 'encrypted', 'token_expires_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
