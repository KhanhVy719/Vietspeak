<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'classroom_id',
    ];

    /**
     * Get the classroom that owns the group.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * The users (students) that belong to the group.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
