<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classroom->groups()->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Đã tạo tổ/nhóm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return back()->with('success', 'Đã xóa tổ/nhóm!');
    }

    /**
     * Add member to group
     */
    public function addMember(Request $request, Group $group)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user is already in the group
        if ($group->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Học sinh này đã có trong nhóm.');
        }

        // Optional: Check if user is in another group of the same classroom
        // To do this, we need to check all groups of the classroom.
        // $classroom = $group->classroom;
        // $inOtherGroup = ...

        $group->users()->attach($user->id);

        return back()->with('success', 'Đã thêm thành viên vào nhóm.');
    }

    /**
     * Remove member from group
     */
    public function removeMember(Group $group, User $user)
    {
        $group->users()->detach($user->id);
        return back()->with('success', 'Đã xóa thành viên khỏi nhóm.');
    }
}
