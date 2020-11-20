<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ShoppingListItem extends Model implements Sortable
{
    use SortableTrait, HasFactory;

    protected $fillable = [
        'order',
        'quantity',
        'checked_off',
    ];

    protected $casts = [
        'checked_off' => 'boolean',
    ];

    public function buildSortQuery()
    {
        return static::query()->where('shopping_list_version_id', $this->shopping_list_version_id);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }
}
