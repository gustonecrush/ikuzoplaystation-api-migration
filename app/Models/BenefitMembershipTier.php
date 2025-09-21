<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenefitMembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_benefit',
        'duration_benefit',
        'kuota_benefit',
        'syarat_benefit',
    ];

    public function membershipTiers()
    {
        return $this->belongsTo(MembershipTier::class, 'id_membership_tier');
    }
}
