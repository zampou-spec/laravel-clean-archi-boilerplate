<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'course_id',
        'playlist_id',
        'price_online',
        'chapter_type',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
