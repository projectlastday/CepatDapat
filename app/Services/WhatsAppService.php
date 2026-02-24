<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Normalize a phone number to digits-only format suitable for wa.me and Fonnte.
     * Rules: strip non-digits, 0xxx → 62xxx, keep 62xxx, reject empty.
     */
    public static function normalizePhone(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $raw);

        if ($digits === '') {
            return null;
        }

        // Convert leading 0 to country code 62 (Indonesia)
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Generate a wa.me link from a normalized phone number.
     */
    public static function waLink(string $normalized): string
    {
        return 'https://wa.me/' . $normalized;
    }

    /**
     * Send a text message via Fonnte API.
     * Returns structured result array; never throws uncaught exceptions.
     */
    public static function sendText(string $target, string $message): array
    {
        $token = config('services.fonnte.token');

        if (!$token) {
            Log::error('WhatsAppService: FONNTE_TOKEN not configured.');
            return ['success' => false, 'status' => 0, 'body' => 'Token not configured'];
        }

        try {
            $response = Http::timeout(config('services.fonnte.timeout_seconds', 5))
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post(config('services.fonnte.base_url', 'https://api.fonnte.com') . '/send', [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $success = $response->successful();

            if (!$success) {
                Log::error('WhatsAppService: Fonnte API non-2xx response.', [
                    'target' => $target,
                    'status' => $response->status(),
                    'body' => mb_substr($response->body(), 0, 200),
                ]);
            }

            return [
                'success' => $success,
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 200),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsAppService: Exception sending message.', [
                'target' => $target,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 0,
                'body' => $e->getMessage(),
            ];
        }
    }
}
