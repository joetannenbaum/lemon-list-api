<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'owner_id',
    ];

    protected $casts = [
        'owner_id' => 'int',
    ];

    public function storeTags()
    {
        return $this->belongsToMany(StoreTag::class, 'item_store_tag');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_item');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
