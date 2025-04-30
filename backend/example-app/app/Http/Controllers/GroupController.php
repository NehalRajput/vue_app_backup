<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\ApiResponse;
use Exception;

class GroupController extends Controller
{
    // GET /api/groups
    public function index()
    {
        try {
            $groups = Group::where('user_id', Auth::id())->get();
            return ApiResponse::success($groups, 'Groups retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong', 500, $e->getMessage());
        }
    }

    // POST /api/groups
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'group_name' => 'required|string|max:255',
            ]);

            $data['user_id'] = Auth::id();
            $group = Group::create($data);

            return ApiResponse::success($group, 'Group created successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to create group', 500, $e->getMessage());
        }
    }

    // GET /api/groups/{id}
    public function show(Group $group)
    {
        try {
            if ($group->user_id !== Auth::id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            return ApiResponse::success($group, 'Group details retrieved');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to retrieve group', 500, $e->getMessage());
        }
    }

    // PUT /api/groups/{id}
    public function update(Request $request, Group $group)
    {
        try {
            if ($group->user_id !== Auth::id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            $data = $request->validate([
                'group_name' => 'required|string|max:255',
            ]);

            $group->update($data);

            return ApiResponse::success($group, 'Group updated successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to update group', 500, $e->getMessage());
        }
    }

    // DELETE /api/groups/{id}
    public function destroy(Group $group)
    {
        try {
            if ($group->user_id !== Auth::id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            $group->delete();

            return ApiResponse::success([], 'Group deleted successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to delete group', 500, $e->getMessage());
        }
    }
}
