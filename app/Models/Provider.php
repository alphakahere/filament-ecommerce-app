<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasFactory;
    
    protected $fillable =[
        'name', 
        'slug', 
        'phone', 
        'address'
    ];

    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }
}
