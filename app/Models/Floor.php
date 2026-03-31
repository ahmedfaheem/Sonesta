<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'manager_id',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return $query;
        }

        return $query->where('manager_id', $user->id);
    }

    public static function generateUniqueNumber(): string
    {
        do {
            $number = (string) random_int(1000, 999999);
        } while (static::query()->where('number', $number)->exists());

        return $number;
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
