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
    ];

    public function userMemberships()
    {
        return $this->hasMany(CustomerMembership::class, 'id_membership');
    }
}
