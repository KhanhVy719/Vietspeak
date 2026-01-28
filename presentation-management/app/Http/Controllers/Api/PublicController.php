<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\TeamMember;

class PublicController extends Controller
{
    /**
     * Get list of active courses for public catalog
     */
    public function courses()
    {
        $courses = Course::where('status', 'active')
            ->select('id', 'name', 'code', 'description', 'price', 'level', 'instructor', 'created_at')
            ->orderBy('created_at', 'desc') // Newest first
            ->get();

        return response()->json([
            'success' => true,
            'data' => $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'description' => $course->description,
                    'price' => $course->price,
                    // If no price column, use default/random for demo or check DB structure
                    // I will check DB structure first, but assuming price exists based on HTML
                    'formatted_price' => number_format($course->price ?? 0, 0, ',', '.') . 'đ',
                    'instructor' => $course->instructor,
                    'level' => $course->level,
                    'thumbnail' => null // Column does not exist yet
                ];
            })
        ]);
    }

    /**
     * Get list of active team members
     */
    public function teamMembers()
    {
        $members = TeamMember::active()
            ->ordered()
            ->get(['id', 'name', 'initials', 'title', 'description', 'avatar_color', 'avatar', 'order']);

        return response()->json([
            'success' => true,
            'data' => $members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'initials' => $member->initials,
                    'title' => $member->title,
                    'description' => $member->description,
                    'avatar_color' => $member->avatar_color,
                    'avatar_url' => $member->avatar_url, // Will be null if no avatar
                    'order' => $member->order
                ];
            })
        ]);
    }
}
