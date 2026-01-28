<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'instructor',
        'duration',
        'level',
        'price',
        'status',
        'start_date',
        'end_date',
        'ai_credits'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['formatted_price'];

    /**
     * Get formatted price with thousand separators
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price == 0 || $this->price === null) {
            return 'Miễn phí';
        }
        return number_format($this->price, 0, ',', '.') . 'đ';
    }

    /**
     * Students enrolled in this course
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot('enrolled_at', 'status', 'progress')
            ->withTimestamps();
    }
}
