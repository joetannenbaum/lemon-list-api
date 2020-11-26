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
        'note',
    ];

    protected $casts = [
        'checked_off'              => 'bool',
        'item_id'                  => 'int',
        'shopping_list_version_id' => 'int',
        'order'                    => 'int',
        'quantity'                    => 'int',
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
