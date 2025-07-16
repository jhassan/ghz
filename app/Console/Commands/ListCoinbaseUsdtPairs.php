<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;



class ListCoinbaseUsdtPairs extends Command
{
    protected $signature = 'coinbase:usdt-pairs';
    protected $description = 'List all Coinbase trading pairs ending with USDT (without dash)';

    public function handle()
    {
        $res = Http::get('https://api.exchange.coinbase.com/products');
        if (!$res->ok()) {
            return $this->error('Failed to fetch products: HTTP ' . $res->status());
        }

        $pairs = collect($res->json())
            ->filter(fn($p) => str_ends_with($p['id'], '-USDT'))
            ->pluck('id')
            ->map(fn($id) => str_replace('-USDT', 'USDT', $id))
            ->sort()
            ->values();

        if ($pairs->isEmpty()) {
            return $this->info('No USDT pairs found.');
        }

        $this->info('📋 USDT Trading Pairs (' . $pairs->count() . '):');
        $pairs->chunk(10)->each(fn($chunk) => $this->line($chunk->join(', ')));
    }
}
