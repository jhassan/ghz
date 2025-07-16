<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchBinanceSymbols extends Command
{
    protected $signature = 'binance:symbols';
    protected $description = 'Fetch all Binance trading symbols and save to a file';

    public function handle()
    {
        $response = Http::get('https://api.binance.com/api/v3/exchangeInfo');

        if (!$response->ok()) {
            $this->error("Failed to fetch Binance exchange info.");
            return;
        }

        $symbols = collect($response->json()['symbols'])
            ->where('status', 'TRADING')
            ->pluck('symbol')
            ->sort()
            ->values();

        Storage::disk('local')->put('binance_symbols.txt', $symbols->implode("\n"));

        $this->info("✅ Binance trading symbols saved to storage/app/binance_symbols.txt");
    }
}
