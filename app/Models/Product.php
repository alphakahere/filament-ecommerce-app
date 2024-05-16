<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'brand_id', 
        'provider_id',
        'slug', 
        'sku', 
        'description', 
        'image',
        'quantity',
        'price',
        'old_price',
        'cost',
        'is_visible', 
        'is_featured', 
        'is_promoted', 
        'is_can_be_returned', 
        'is_can_be_shipped', 
        'is_available',
        'options',
        'published_at'
    ];

    public function brand():BelongsTo {
        return $this->belongsTo(Brand::class);
    }

    public function provider():BelongsTo {
        return $this->belongsTo(Provider::class);
    }

    public function categories():BelongsToMany {
        return $this->BelongsToMany(Category::class);
    }

    public function variations():HasMany {
        return $this->hasMany(Variation::class);
    }

    protected $casts = [
        'options' => 'json',
    ];

    
}
