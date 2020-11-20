<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingListVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class)->ordered();
    }

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function scopeActive($q)
    {
        return $q->whereNull('archived_at');
    }
}
