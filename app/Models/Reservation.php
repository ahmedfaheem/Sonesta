<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'client_id',
        'room_id',
        'accompany_number',
        'paid_price',
        'reservation_date',
    ];

    protected function casts(): array
    {
        return [
            'accompany_number' => 'integer',
            'paid_price' => 'integer',
            'reservation_date' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
