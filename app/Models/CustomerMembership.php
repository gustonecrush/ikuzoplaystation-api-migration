<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_membership',
        'start_periode',
        'end_periode',
        'status_tier',
        'status_benefit',
        'status_payment',
        'status_birthday_treat',
        'kuota_weekly',
        'membership_count',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class, 'id_membership');
    }
}
