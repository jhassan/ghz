<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CryptoSymbolsMail;

class SendCryptoSymbolsEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-crypto-symbols-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $symbols = ["BTC", "ICP", "DOT", "NEAR", "ORDI", "INJ", "NEIRO", "SOL", "OP", "XRP", "ADA"];
        $symbols = ["BTCUSDT", "ICPUSDT", "DOTUSDT", "NEARUSDT", "ORDIUSDT", "INJUSDT", "NEIROUSDT", "SOLUSDT", "OPUSDT", "XRPUSDT", "ADAUSDT"];
        // get latest record per symbol
        $cryptoData = DB::table('candles as cp')
            ->join(DB::raw("(SELECT symbol, MAX(created_at) as last_created
                             FROM candles
                             WHERE symbol IN ('" . implode("','", $symbols) . "')
                             GROUP BY symbol) latest"),
                function ($join) {
                    $join->on('cp.symbol', '=', 'latest.symbol')
                         ->on('cp.created_at', '=', 'latest.last_created');
                })
            ->get();

        // send email
        Mail::to('jawadjee0519@gmail.com')->send(new CryptoSymbolsMail($cryptoData));

        $this->info('Crypto symbols email sent successfully!');
    }
}
