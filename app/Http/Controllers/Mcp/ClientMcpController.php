<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientMcpController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Client::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->query('active_only')) {
            $query->where('is_active', true);
        }

        $clients = $query->orderBy('created_at', 'desc')
            ->limit($request->query('limit', 50))
            ->get(['id', 'name', 'email', 'phone', 'property_address', 'is_active', 'user_provisioned_at', 'last_login_at', 'created_at']);

        return response()->json([
            'count' => $clients->count(),
            'clients' => $clients,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $client = Client::findOrFail($id);

        return response()->json([
            'client' => $client,
            'provisioning_status' => $client->provisioning_status_label,
            'has_google' => $client->hasGoogleLinked(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', 'unique:clients,email'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'property_address' => ['nullable', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:5000'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $client = Client::create($validated + ['is_active' => $validated['is_active'] ?? true]);

        return response()->json(['client' => $client], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'property_address' => ['nullable', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:5000'],
            'is_active'        => ['sometimes', 'boolean'],
        ]);

        $client->update($validated);

        return response()->json(['client' => $client->fresh()]);
    }
}
