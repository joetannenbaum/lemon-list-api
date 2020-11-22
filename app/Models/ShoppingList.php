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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_shopping_list')->withTimestamps();
    }
}
