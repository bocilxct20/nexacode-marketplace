<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, MidtransService $midtrans)
    {
        Log::info('Midtrans Webhook Received', $request->all());

        $success = $midtrans->handleNotification($request->all());

        return response()->json([
            'status' => $success ? 'success' : 'failed'
        ], $success ? 200 : 400);
    }
}
