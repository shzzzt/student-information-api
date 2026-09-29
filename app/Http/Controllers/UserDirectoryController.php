<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDirectoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = User::query()
            ->select(['id', 'first_name', 'last_name', 'email', 'role', 'status'])
            ->where('status', 'active');

        if ($request->query('role')) {
            $query->where('role', $request->query('role'));
        }

        return $this->success('Users retrieved successfully.', $query->orderBy('last_name')->get());
    }
}
