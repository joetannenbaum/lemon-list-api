<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
