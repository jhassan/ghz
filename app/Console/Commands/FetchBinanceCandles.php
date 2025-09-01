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
        $coins = ["BTC", "ICP", "DOT", "NEAR", "ORDI", "INJ", "NEIRO", "SOL", "OP", "XRP", "ADA"];
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

            $lastClosedIndex = count($candles) - 2; // ✅ آخری بند candle تک ہی loop
            for ($index = 1; $index <= $lastClosedIndex; $index++) {
                $prev = $candles[$index - 1];
                $current = $candles[$index];

                $prevOpen = (float) $prev[1];
                $prevClose = (float) $prev[4];
                $currOpen = (float) $current[1];
                $currClose = (float) $current[4];

                // ✅ Basic engulfing condition
                $isBasicEngulfing = (
                    $prevOpen > $prevClose &&   // پچھلی bearish
                    $currClose > $currOpen &&   // موجودہ bullish
                    $currOpen <= $prevClose &&
                    $currClose > $prevOpen
                );

                if (! $isBasicEngulfing) continue;

                // =====================
                // ✅ Volume spike check
                // =====================
                $volume     = (float) $current[5];
                $prevVolume = (float) $prev[5];
                $isVolumeDriven = $volume > ($prevVolume * 1.5);  // 1.5x زیادہ
                // =====================
                // ✅ BTC base check
                // =====================
                $isBTCDriven = false;
                if ($c === "BTC") {
                    $isBTCDriven = true; // اگر خود BTC ہے
                } else {
                    $btcResp = Http::get('https://api.binance.com/api/v3/klines', [
                        'symbol' => 'BTCUSDT',
                        'interval' => $interval,
                        'limit' => 3,
                    ]);
                    if ($btcResp->ok()) {
                        $btcCandle = $btcResp->json();
                        $btcPrev = $btcCandle[count($btcCandle) - 2];
                        $btcOpen = (float) $btcPrev[1];
                        $btcClose = (float) $btcPrev[4];
                        if ($btcClose > $btcOpen && $currClose > $currOpen) {
                            $isBTCDriven = true;
                        }
                    }
                }

                // =====================
                // ✅ Dominance base check (BTCDOMUSDT Futures symbol)
                // =====================
                $isDomDriven = false;
                $domResp = Http::get('https://fapi.binance.com/fapi/v1/klines', [
                    'symbol' => 'BTCDOMUSDT',
                    'interval' => $interval,
                    'limit' => 3,
                ]);
                if ($domResp->ok()) {
                    $domCandle = $domResp->json();
                    $domPrev = $domCandle[count($domCandle) - 2];
                    $domOpen = (float) $domPrev[1];
                    $domClose = (float) $domPrev[4];
                    $domChange = $domClose - $domOpen;

                    if ($c === "BTC" && $domChange > 0) {
                        $isDomDriven = true;   // BTC کے لیے dominance اوپر جانا
                    }
                    if ($c !== "BTC" && $domChange < 0) {
                        $isDomDriven = true;   // Alts کے لیے dominance نیچے جانا
                    }
                }

                // =====================
                // ✅ Basis log
                // =====================
                $basis = [];
                if ($isBTCDriven)    $basis[] = "BTC";
                if ($isDomDriven)    $basis[] = "DOMINANCE";
                if ($isVolumeDriven) $basis[] = "VOLUME";
                $basisStr = empty($basis) ? "UNCLASSIFIED" : implode("|", $basis);

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

                $this->info("✅ Engulfing FOUND — $symbol | OpenTime: $openTime | O: $currOpen C: $currClose | Basis: $basisStr");
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
