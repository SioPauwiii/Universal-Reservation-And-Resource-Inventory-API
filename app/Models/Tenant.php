<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'owner_email',
        'owner_name',
        'contact_number',
        'address',
        'business_email',
        'business_description',
        'status',
        'plan',
        'settings',
    ];
}