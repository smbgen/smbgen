<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserMcpController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->limit($request->query('limit', 50))
            ->get(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at']);

        return response()->json([
            'count' => $users->count(),
            'users' => $users,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at']),
            'has_google_calendar' => $user->hasGoogleCalendar(),
            'is_administrator' => $user->isAdministrator(),
            'is_client' => $user->isClient(),
        ]);
    }
}
