<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Models\LeadForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadMcpController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LeadForm::with('cmsPage:id,title,slug');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $leads = $query->orderBy('created_at', 'desc')
            ->limit($request->query('limit', 50))
            ->get();

        return response()->json([
            'count' => $leads->count(),
            'leads' => $leads,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $lead = LeadForm::with('cmsPage:id,title,slug')->findOrFail($id);

        return response()->json(['lead' => $lead]);
    }
}
