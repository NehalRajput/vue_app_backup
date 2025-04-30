<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\ApiResponse;

class GroupController extends Controller
{
    // GET /api/groups
    public function index()
    {
        $groups = Group::where('user_id', Auth::id())->get();
        return ApiResponse::success($groups, 'Groups retrieved successfully');
    }

    // POST /api/groups
    public function store(Request $request)
    {
        $data = $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        $data['user_id'] = Auth::id(); // Set logged-in user
        $group = Group::create($data);

        return ApiResponse::success($group, 'Group created successfully');
    }

    // GET /api/groups/{id}
    public function show(Group $group)
    {
        if ($group->user_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized');
        }

        return ApiResponse::success($group, 'Group details retrieved');
    }

    // PUT /api/groups/{id}
    public function update(Request $request, Group $group)
    {
        if ($group->user_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized');
        }

        $data = $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        $group->update($data);

        return ApiResponse::success($group, 'Group updated successfully');
    }

    // DELETE /api/groups/{id}
    public function destroy(Group $group)
    {
        if ($group->user_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized');
        }

        $group->delete();

        return ApiResponse::success([], 'Group deleted successfully');
    }
}
