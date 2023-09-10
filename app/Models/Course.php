<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'price_online',
        'price_classroom',
        'description',
    ];

    protected $casts = [
        'price_online' => 'double',
        'price_classroom' => 'double',
    ];

    public function subscribes(): HasMany
    {
        return $this->hasMany(Subscribe::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
