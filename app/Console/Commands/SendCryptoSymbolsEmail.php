<?php

namespace App\Console\Commands;

use App\Mail\CryptoSymbolsMail;
use App\Models\Coins;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        sleep(30);
        // $symbols = ["BTC", "ICP", "DOT", "NEAR", "ORDI", "INJ", "NEIRO", "SOL", "OP", "XRP", "ADA"];
        // $symbols = ['BTCUSDT', 'ICPUSDT', 'DOTUSDT', 'NEARUSDT', 'ORDIUSDT', 'INJUSDT', 'NEIROUSDT', 'SOLUSDT', 'OPUSDT', 'XRPUSDT', 'ADAUSDT'];
        $record = Coins::find(1); // or ->first()
        // remove double quotes first
        $clean = str_replace('"', '', $record->coins);

        // now split by comma
        $symbols = array_map('trim', explode(',', $clean));
        // get latest record per symbol
        // $cryptoData = DB::table('candles as cp')
        //     ->join(DB::raw("(SELECT symbol, MAX(created_at) as last_created
        //                      FROM candles
        //                      WHERE symbol IN ('".implode("','", $symbols)."')
        //                      GROUP BY symbol) latest"),
        //         function ($join) {
        //             $join->on('cp.symbol', '=', 'latest.symbol')
        //                 ->on('cp.created_at', '=', 'latest.last_created');
        //         })
        //     ->get();

        $now = Carbon::now()->second(0);   // current time with seconds = 0
        $startTime = $now->copy();         // e.g. 07:00:00
        $endTime = $now->copy()->addSeconds(30); // e.g. 07:00:30

        $cryptoData = DB::table('candles as cp')
            ->join(DB::raw("(SELECT symbol, MAX(created_at) as last_created
                            FROM candles
                            WHERE symbol IN ('".implode("','", $symbols)."')
                            GROUP BY symbol) latest"),
                function ($join) {
                    $join->on('cp.symbol', '=', 'latest.symbol')
                        ->on('cp.created_at', '=', 'latest.last_created');
                })
            ->whereBetween('cp.created_at', [$startTime, $endTime])
            ->get();

        // send email
        if (count($cryptoData) > 0) {
            $sendEmail = Mail::to('jawadjee0519@gmail.com')->send(new CryptoSymbolsMail($cryptoData));
            $sendEmail = Mail::to('scalpinguniverseg@gmail.com')->send(new CryptoSymbolsMail($cryptoData));
            // dd($sendEmail);
            $this->info('Crypto symbols email sent successfully!');
        } else {
            $this->info('Crypto symbols not found!');
        }

    }
}
