<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    public function versions()
    {
        return $this->hasMany(ShoppingListVersion::class);
    }

    public function activeVersion()
    {
        return $this->hasOne(ShoppingListVersion::class)->active();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
