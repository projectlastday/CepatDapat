<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAuctionSoldNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 60, 180];
    public int $timeout = 60;

    // Scalar payload — stable across serialization
    protected int $auctionId;
    protected string $auctionName;
    protected int $ownerId;
    protected string $ownerName;
    protected ?string $ownerPhone;
    protected int $winnerId;
    protected string $winnerName;
    protected ?string $winnerPhone;
    protected int $winningBid;

    public function __construct(
        int $auctionId,
        string $auctionName,
        int $ownerId,
        string $ownerName,
        ?string $ownerPhone,
        int $winnerId,
        string $winnerName,
        ?string $winnerPhone,
        int $winningBid
    ) {
        $this->auctionId = $auctionId;
        $this->auctionName = $auctionName;
        $this->ownerId = $ownerId;
        $this->ownerName = $ownerName;
        $this->ownerPhone = $ownerPhone;
        $this->winnerId = $winnerId;
        $this->winnerName = $winnerName;
        $this->winnerPhone = $winnerPhone;
        $this->winningBid = $winningBid;
    }

    public function handle(): void
    {
        $ownerNorm = WhatsAppService::normalizePhone($this->ownerPhone);
        $winnerNorm = WhatsAppService::normalizePhone($this->winnerPhone);

        // --- Message to Owner ---
        if ($ownerNorm) {
            $winnerContact = $winnerNorm
                ? WhatsAppService::waLink($winnerNorm)
                : '(nomor tidak tersedia)';

            $hargaFormatted = number_format($this->winningBid, 0, ',', '.');
            $ownerMessage = "Lelang *{$this->auctionName}* telah selesai, pemenangnya adalah *{$this->winnerName}* dengan penawaran *Rp {$hargaFormatted}*. Berikut adalah nomor WA nya untuk menyelesaikan transaksi: {$winnerContact}";

            $ownerResult = WhatsAppService::sendText($ownerNorm, $ownerMessage);

            Log::info('AuctionSoldNotification: Owner message sent.', [
                'auction_id' => $this->auctionId,
                'role' => 'owner',
                'target' => $ownerNorm,
                'success' => $ownerResult['success'],
                'http_status' => $ownerResult['status'],
            ]);
        } else {
            Log::warning('AuctionSoldNotification: Owner phone invalid/missing, skipping.', [
                'auction_id' => $this->auctionId,
                'owner_id' => $this->ownerId,
            ]);
        }

        // --- Message to Winner ---
        if ($winnerNorm) {
            $ownerContact = $ownerNorm
                ? WhatsAppService::waLink($ownerNorm)
                : '(nomor tidak tersedia)';

            $hargaFormatted = number_format($this->winningBid, 0, ',', '.');
            $winnerMessage = "Anda telah memenangkan lelang *{$this->auctionName}* dengan harga *Rp {$hargaFormatted}*. Berikut adalah nomor WA *{$this->ownerName}* untuk menyelesaikan transaksi: {$ownerContact}";

            $winnerResult = WhatsAppService::sendText($winnerNorm, $winnerMessage);

            Log::info('AuctionSoldNotification: Winner message sent.', [
                'auction_id' => $this->auctionId,
                'role' => 'winner',
                'target' => $winnerNorm,
                'success' => $winnerResult['success'],
                'http_status' => $winnerResult['status'],
            ]);
        } else {
            Log::warning('AuctionSoldNotification: Winner phone invalid/missing, skipping.', [
                'auction_id' => $this->auctionId,
                'winner_id' => $this->winnerId,
            ]);
        }
    }
}
