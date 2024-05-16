<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug','url','category_id','is_visible', 'description'];

    public function category():BelongsTo {
        return $this->belongsTo(Category::class);
    }

}
