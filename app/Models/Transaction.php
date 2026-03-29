<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'origin_office_id',
        'destination_office_id',
        'category_id',
        'name',
        'note',
        'value',
        'price',
        'adjustment',
        'price_id',
        'final_price',
        'creator_id',
        'status',
    ];
}

