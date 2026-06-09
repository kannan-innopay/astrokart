<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanEntitlement extends Model
{
    protected $fillable = [
        'plan',
        'entitlement',
    ];
}
