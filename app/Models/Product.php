<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['name', 'category', 'description', 'icon', 'status'];
}
