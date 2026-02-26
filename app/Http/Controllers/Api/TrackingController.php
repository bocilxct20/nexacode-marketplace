<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserInteraction;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'product_id' => 'nullable|integer',
            'payload' => 'nullable|array',
            'url' => 'nullable|string',
        ]);

        UserInteraction::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
            'type' => $validated['type'],
            'payload' => $validated['payload'],
            'url' => $validated['url'] ?? $request->header('referer'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['status' => 'success']);
    }
}
