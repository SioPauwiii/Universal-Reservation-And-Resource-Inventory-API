<?php

// reservation model
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'reserved_at',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}