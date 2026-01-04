<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'is_active',
        'is_pinned',
        'published_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'warning',
            'success' => 'success',
            'danger' => 'danger',
            default => 'info',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'heroicon-o-exclamation-triangle',
            'success' => 'heroicon-o-check-circle',
            'danger' => 'heroicon-o-x-circle',
            default => 'heroicon-o-information-circle',
        };
    }
}
