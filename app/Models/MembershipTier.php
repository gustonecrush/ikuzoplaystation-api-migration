<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'price',
        'period',
        'benefits',
        'icon',
        'benefit_reset_time'
    ];

    public function userMemberships()
    {
        return $this->hasMany(CustomerMembership::class, 'id_membership');
    }

    public function benefitMemberhips()
    {
        return $this->hasMany(BenefitMembershipTier::class, 'id_membership_tier');
    }
}
