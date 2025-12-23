<?php

// Model for Item entity
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at',
    // ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasOne(ItemDetail::class, 'item_id');
    }
}