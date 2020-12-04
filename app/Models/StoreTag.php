<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class StoreTag extends Model implements Sortable
{
    use SortableTrait, HasFactory;

    protected $fillable = [
        'name',
        'order',
    ];

    protected $casts = [
        'store_id' => 'int',
        'order'    => 'int',
    ];

    public function buildSortQuery()
    {
        return static::query()->where('store_id', $this->store_id);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
