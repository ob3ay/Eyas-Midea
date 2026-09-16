<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'recipient_user_id',
        'type',
        'title',
        'message',
        'read_at',
        'isread',
        'related_entity_type',
        'related_entity_id',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'isread' => 'boolean',
        ];
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function relatedEntity(): MorphTo
    {
        return $this->morphTo('related_entity');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): bool
    {
        if ($this->isRead()) {
            return false;
        }

        return $this->update([
            'read_at' => now(),
            'isread' => true,
        ]);
    }
}
