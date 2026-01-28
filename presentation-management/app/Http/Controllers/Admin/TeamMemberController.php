<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        $members = TeamMember::ordered()->get();
        return view('admin.team.index', compact('members'));
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initials' => 'required|string|size:2',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'avatar_color' => 'required|string|max:7',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'required|integer|min:0',
        ]);

        // Handle checkbox - if not present, set to false
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('team_avatars', 'public');
        }

        TeamMember::create($validated);

        return redirect()->route('admin.team.index')
            ->with('success', 'Thành viên đã được thêm!');
    }

    public function edit(TeamMember $team)
    {
        return view('admin.team.edit', compact('team'));
    }

    public function update(Request $request, TeamMember $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initials' => 'required|string|size:2',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'avatar_color' => 'required|string|max:7',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'required|integer|min:0',
        ]);

        // Handle checkbox - if not present, set to false (0)
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Handle new avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($team->avatar) {
                Storage::disk('public')->delete($team->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('team_avatars', 'public');
        }

        $team->update($validated);

        return redirect()->route('admin.team.index')
            ->with('success', 'Thành viên đã được cập nhật!');
    }

    public function destroy(TeamMember $team)
    {
        // Delete avatar if exists
        if ($team->avatar) {
            Storage::disk('public')->delete($team->avatar);
        }

        $team->delete();

        return redirect()->route('admin.team.index')
            ->with('success', 'Thành viên đã được xóa!');
    }
}
