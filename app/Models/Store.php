<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    public function tags()
    {
        return $this->hasMany(StoreTag::class)->orderBy('order');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
