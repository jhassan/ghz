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
        $symbols = [
            "ICP", "DOT", "NEAR", "ORDI", "INJ", "NEIRO", "SOL", "XRP", "ADA"
        ];
        $interval = "15m";
        $max_gap_minutes = 180; // 3 hours
        $engulf_list = [];

        foreach ($symbols as $coin) {
            $candles = $this->fetchCandle($coin, $interval);
            dd($candles);
            if (!$candles || count($candles) < 2) continue;

            $prev = $candles[0];
            $curr = $candles[1];

            if ($this->isEngulfing($prev, $curr)) {
                $now = time();
                $engulf_key = $coin;

                if (!isset($engulf_list[$engulf_key])) {
                    // First engulf — wait for 15min close (mocked with current close time)
                    $closeTime = intval($curr[6] / 1000);
                    if ($now >= $closeTime) {
                        $engulf_list[$engulf_key] = ["last" => $now, "count" => 2];
                        $influence = $this->detectInfluence($coin);
                        echo "$coin | Engulf #2 | $influence\n";
                    } else {
                        // Do not notify yet — waiting for 15m close
                        continue;
                    }
                } else {
                    $lastTime = $engulf_list[$engulf_key]["last"];
                    $diff = ($now - $lastTime) / 60;
                    if ($diff <= $max_gap_minutes) {
                        $engulf_list[$engulf_key]["count"]++;
                        $engulf_list[$engulf_key]["last"] = $now;
                        $engulfNo = $engulf_list[$engulf_key]["count"];
                        $influence = detectInfluence($coin);
                        echo "$coin | Engulf #$engulfNo | $influence\n";
                    } else {
                        // Reset on 3hr gap
                        $engulf_list[$engulf_key] = ["last" => $now, "count" => 2];
                        $influence = detectInfluence($coin);
                        echo "$coin | Engulf #2 | $influence\n";
                    }
                }
            }
            // Else: no valid engulf
        }


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
