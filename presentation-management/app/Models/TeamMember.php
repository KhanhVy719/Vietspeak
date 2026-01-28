<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'initials',
        'title',
        'description',
        'avatar_color',
        'avatar',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Scope to get only active members
     */
    public function scopeActive($query)
    {
        return $query->whereRaw('CAST(is_active AS boolean) = true');
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
