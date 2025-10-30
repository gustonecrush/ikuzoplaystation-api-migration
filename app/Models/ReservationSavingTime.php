<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationSavingTime extends Model
{
    protected $fillable = [
        'id_reservation',
        'date_saving',
        'start_time_saving',
        'end_time_saving',
        'is_active'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'id_reservation');
    }
}
