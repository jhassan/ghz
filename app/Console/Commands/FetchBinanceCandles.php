<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Candle;
use Carbon\Carbon;

class FetchBinanceCandles extends Command
{
    protected $signature = 'binance:fetch {symbols=BTCUSDT,ETHUSDT,NEIROUSDT}';
    protected $description = 'Fetch 15-min candlestick data from Binance and save valid bullish engulfing spot candles for multiple coins';

    public function handle()
    {

          // 🔁 Hardcoded list of coin pairs
             // ---------------- CONFIG ----------------
        $coins = ["BTC", "ICP", "DOT", "NEAR", "ORDI", "INJ", "NEIRO", "SOL", "OP", "XRP", "ADA"];
        // $coins = ["BTCUSDT", "ICPUSDT", "DOTUSDT", "NEARUSDT", "ORDIUSDT", "INJUSDT", "NEIROUSDT", "SOLUSDT", "OPUSDT", "XRPUSDT", "ADAUSDT"];
        $interval = '15m';
        $limit = 3; // ✅ Changed from 73 to 3
        // ----------------------------------------

        foreach ($coins as $c) {
            $symbol = $c . 'USDT';
            $this->info("\n🔎 Checking: $symbol");

            // Fetch klines
            $response = Http::get('https://api.binance.com/api/v3/klines', [
                'symbol' => $symbol,
                'interval' => $interval,
                'limit' => $limit,
            ]);

            if (! $response->ok()) {
                $this->error("❌ Failed to fetch candles for $symbol — HTTP " . $response->status());
                continue;
            }

            $candles = $response->json();

            for ($index = 1; $index < count($candles); $index++) {
                $prev = $candles[$index - 1];
                $current = $candles[$index];

                $prevOpen = (float) $prev[1];
                $prevClose = (float) $prev[4];
                $currOpen = (float) $current[1];
                $currClose = (float) $current[4];

                // $tolerance = 0.000001;

                // $isEngulfing = (
                //     $prevOpen > $prevClose &&
                //     $currClose > $currOpen &&
                //     abs($prevClose - $currOpen) < $tolerance &&
                //     $currClose > $prevOpen
                // );
                $isEngulfing = (
                    $prevOpen > $prevClose &&   // پچھلی bearish
                    $currClose > $currOpen &&   // موجودہ bullish
                    $currOpen <= $prevClose &&  // موجودہ open پچھلی close سے کم یا برابر
                    $currClose > $prevOpen      // موجودہ close پچھلی open سے اوپر
                );

                if (! $isEngulfing) continue;

                $openTime = Carbon::createFromTimestampMs($current[0])->setTimezone('Asia/Karachi');

                if (Candle::where('symbol', $symbol)->where('open_time', $openTime)->exists()) {
                    $this->info("⏭ Already recorded: $symbol at $openTime");
                    continue;
                }

                Candle::create([
                    'symbol' => $symbol,
                    'interval' => $interval,
                    'open_time' => $openTime,
                    'open' => $currOpen,
                    'high' => (float) $current[2],
                    'low' => (float) $current[3],
                    'close' => $currClose,
                    'is_bullish_engulfing' => true,
                ]);

                $this->info("✅ Engulfing FOUND — $symbol | OpenTime: $openTime | O: $currOpen C: $currClose");
            }
        }

        $this->info("\n🎯 Scan complete — checked " . count($coins) . " coins.");
        // return 0;
        $this->info("🎯 Finished scanning all symbols.");
    }

    public function fetchCandle($symbol, $interval) {
        $url = "https://api.binance.com/api/v3/klines?symbol={$symbol}USDT&interval={$interval}&limit=2";
        $data = json_decode(file_get_contents($url), true);
        return $data;
    }

    public function isEngulfing($previous, $current) {
        $prevOpen = floatval($previous[1]);
        $prevClose = floatval($previous[4]);
        $currOpen = floatval($current[1]);
        $currClose = floatval($current[4]);

        $prevBody = abs($prevClose - $prevOpen);
        $currBody = abs($currClose - $currOpen);

        // Engulfing condition
        $engulf = $currOpen <= $prevClose && $currClose >= $prevOpen;

        // Red candle must be at least 25% of green body
        $minRedBody = $currBody * 0.25;
        $validRed = $prevBody >= $minRedBody;

        return ($prevOpen > $prevClose && $currClose > $currOpen && $engulf && $validRed);
    }

    public function detectInfluence($symbol) {
        // Mocked logic — replace with real BTC/DOM comparison if needed
        return rand(0, 1) ? "Own Volume" : "BTC Influence";
    }
}
