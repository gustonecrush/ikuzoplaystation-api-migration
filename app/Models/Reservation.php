<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_membership', 'id');
    }

    public function savingTimes()
    {
        return $this->hasMany(ReservationSavingTime::class, 'id_reservation', 'id');
    }
}
