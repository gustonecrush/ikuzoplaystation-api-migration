<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceFacility extends Model
{
    use HasFactory;

    protected $table = "maintenance_facilities";
    protected $guarded = ['id'];
}
