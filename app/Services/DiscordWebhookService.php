<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordWebhookService
{
    private const WEBHOOKS = [
        'cancel' => 'https://discord.com/api/webhooks/1471767205883543689/kLzzYBGdOPfxnGEEMDXes3X9pvebs8xE8UJAZvTGHt3I2Vk8aJEUOsh9ijQFEst5Nv_K',
        'uncancel' => 'https://discord.com/api/webhooks/1471768027488845868/8SuJTfAzpNONq-etAUVKWyVfsLLzZ3Im9WOkqRRn3a-zHiB91ZimHOk4fGMJHC4FH0QA',
        'delete' => 'https://discord.com/api/webhooks/1471768361716289628/Nn7Ctg1CkFk_2gkbeW4WmrXy_E9K0qShatVu4ErFvpJpF19wSHq3Ue4h0KB8Q_K4RmeW',
        'restore' => 'https://discord.com/api/webhooks/1471768563499929742/UhaXF7EEeeTO2jxd6hq3e7tpbFuq3Ro7PHi92TOMaJDJeYMw7I0yIFCjnP3GIqidH7WT',
    ];

    private const TITLES = [
        'cancel' => '🚫 Lelang Dibatalkan',
        'delete' => '🗑️ Lelang Dihapus',
        'uncancel' => '✅ Lelang Di-uncancel',
        'restore' => '♻️ Lelang Direstore',
    ];

    private const COLORS = [
        'cancel' => 0xE74C3C, // red
        'delete' => 0xE67E22, // orange
        'uncancel' => 0x2ECC71, // green
        'restore' => 0x3498DB, // blue
    ];

    /**
     * Send a Discord webhook notification.
     *
     * @param string $type      One of: cancel, delete, uncancel, restore
     * @param string $username  The actor's username
     * @param string $namaBarang The auction item name
     * @param string $alasan    The reason
     */
    public static function send(string $type, string $username, string $namaBarang, string $alasan): void
    {
        $url = self::WEBHOOKS[$type] ?? null;
        if (!$url) {
            return;
        }

        try {
            Http::post($url, [
                'embeds' => [
                    [
                        'title' => self::TITLES[$type] ?? $type,
                        'color' => self::COLORS[$type] ?? 0x95A5A6,
                        'fields' => [
                            ['name' => 'Pelaku', 'value' => $username, 'inline' => true],
                            ['name' => 'Nama Barang', 'value' => $namaBarang, 'inline' => true],
                            ['name' => 'Alasan', 'value' => $alasan, 'inline' => false],
                        ],
                        'timestamp' => now()->toIso8601String(),
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Discord webhook failed (' . $type . '): ' . $e->getMessage());
        }
    }
}
