<?php

namespace App\Http\Controllers;

use App\Services\AsaasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsaasWebhookController extends Controller
{
    public function handle(Request $request, AsaasService $asaas): JsonResponse
    {
        $token = $request->header('asaas-access-token');

        if ($token !== config('services.asaas.webhook_token')) {
            // Return 200 to avoid Asaas retrying (silent ignore for invalid tokens)
            return response()->json(['status' => 'ok']);
        }

        $asaas->handleWebhook($request->all());

        return response()->json(['status' => 'ok']);
    }
}
