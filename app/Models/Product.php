<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $guarded = [];

    public function images()
    {
        return $this->hasMany(Images::class);
    }

    public function productvariant()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
