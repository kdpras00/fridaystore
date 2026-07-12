<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify webhook token
        $token = $request->header('x-callback-token');
        if ($token !== config('services.xendit.webhook_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload   = $request->all();
        $status    = strtolower($payload['status'] ?? '');
        $externalId = $payload['external_id'] ?? null;

        if (!$externalId) {
            return response()->json(['message' => 'Missing external_id'], 400);
        }

        $transaksi = Transaksi::where('no_invoice', $externalId)->first();
        if (!$transaksi) {
            // Not our invoice — return 200 so Xendit stops retrying
            return response()->json(['message' => 'ignored']);
        }

        if (in_array($status, ['settled', 'paid'])) {
            $transaksi->update(['payment_status' => 'paid']);
        } elseif ($status === 'expired') {
            $transaksi->update(['payment_status' => 'expired']);
        }

        return response()->json(['message' => 'ok']);
    }
}
