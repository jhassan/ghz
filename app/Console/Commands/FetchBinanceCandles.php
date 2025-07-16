<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Candle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FetchBinanceCandles extends Command
{
    protected $signature = 'binance:fetch {symbols=BTCUSDT,ETHUSDT,NEIROUSDT}';
    protected $description = 'Fetch 15-min candlestick data from Binance and save valid bullish engulfing spot candles for multiple coins';

    public function handle()
    {

          // 🔁 Hardcoded list of coin pairs
        $symbols = [
            'ACHUSDT', 'ADAUSDT', 'AGLDUSDT', 'AIOZUSDT', 'ALCXUSDT', 'APEUSDT', 'API3USDT', 'APTUSDT', 'ARPAUSDT', 'ASMUSDT'
,'ATAUSDT', 'ATOMUSDT', 'AUCTIONUSDT', 'AVAXUSDT', 'AXSUSDT', 'BADGERUSDT', 'BICOUSDT', 'BITUSDT', 'BOBAUSDT', 'BONDUSDT',
'BTCUSDT', 'BTRSTUSDT', 'C98USDT', 'CHZUSDT', 'CLVUSDT', 'COVALUSDT', 'CROUSDT', 'CTXUSDT', 'DDXUSDT', 'DESOUSDT',
'DIAUSDT', 'DOGEUSDT', 'DOTUSDT', 'DREPUSDT', 'DYPUSDT', 'ELAUSDT', 'ENJUSDT', 'ENSUSDT', 'ERNUSDT', 'ETHUSDT',
'FARMUSDT', 'FETUSDT', 'FIDAUSDT', 'FISUSDT', 'FLOWUSDT', 'FORTUSDT', 'FOXUSDT', 'GALAUSDT', 'GALUSDT', 'GMTUSDT',
'GNOUSDT', 'HBARUSDT', 'HFTUSDT', 'HOPRUSDT', 'ICPUSDT', 'IDEXUSDT', 'IMXUSDT', 'INDEXUSDT', 'JASMYUSDT', 'KRLUSDT',
'KSMUSDT', 'LCXUSDT', 'LINKUSDT', 'LQTYUSDT', 'LRCUSDT', 'MASKUSDT', 'MATHUSDT', 'MATICUSDT', 'MCO2USDT', 'MDTUSDT',
'MEDIAUSDT', 'METISUSDT','MINAUSDT', 'NCTUSDT', 'NEARUSDT', 'NESTUSDT', 'OPUSDT', 'ORNUSDT', 'PAXUSDT', 'PERPUSDT',
'POLSUSDT', 'POLYUSDT', 'PONDUSDT', 'POWRUSDT', 'PRQUSDT', 'QNTUSDT', 'QSPUSDT', 'RADUSDT', 'REQUSDT', 'RLYUSDT',
'RNDRUSDT', 'ROSEUSDT', 'SANDUSDT', 'SHIBUSDT', 'SHPINGUSDT', 'SOLUSDT', 'SPELLUSDT', 'STGUSDT', 'STXUSDT', 'SUKUUSDT',
'SUPERUSDT', 'SYLOUSDT', 'TIMEUSDT', 'TRACUSDT', 'TRUUSDT', 'UPIUSDT', 'USTUSDT', 'VGXUSDT', 'WAMPLUSDT', 'WCFGUSDT',
'WLUNAUSDT', 'XCNUSDT', 'XLMUSDT', 'XRPUSDT', 'XYOUSDT', 'ZENUSDT'
        ];

        // $symbols = [
        //     "FIS", "MDT", "VIC", "REI", "LOKA", "PIVX", "VOXEL", "MBOX", "COW", "FIO", "BEL", "NKN", "1MBABYDOGE", "ASR",
        //     "ADX", "GTC", "SCRT", "CVC", "SYS", "CYBER", "IDEX", "RPL", "A", "HOOK", "DEGO", "NTRN", "CVX", "ATA", "CELO",
        //     "D", "DUSK", "CKB", "HIFI", "UNI", "GLMR", "LSK", "SC", "1INCH", "POND", "GLM", "SYN", "SUPER", "QKC", "BAR",
        //     "TNSR", "UTK", "MOVR", "POL", "SLF", "FLUX", "STO", "FET", "HIVE", "ALCX", "FARM", "ZEC", "G", "OXT", "WAN",
        //     "BTTC", "SHIB", "POLYX", "YGG", "NFP", "WAXP", "ARB", "FLOKI", "DODO", "BICO", "API3", "ONE", "MLN", "BAT",
        //     "SKL", "XLM", "TFUEL", "USUAL", "DENT", "TLM", "HIGH", "STEEM", "SAGA", "DYDX", "SLP", "PIXEL", "DEXE", "SSV",
        //     "AUDIO", "XNO", "XTZ", "WIN", "POWR", "TRB", "FIL", "IQ", "ETH", "WBETH", "RUNE", "FXS", "GNS", "LUMIA", "TKO",
        //     "ATM", "GNO", "PENGU", "DYM", "REQ", "RDNT", "TRU", "OGN", "AVA", "CATI", "FLM", "EGLD", "XVG", "ICX", "GMX",
        //     "C98", "STORJ", "QI", "ZK", "WCT", "SAND", "MKR", "CHR", "ALGO", "ATOM", "MBL", "ENJ", "FLOW", "PEPE", "HOT",
        //     "ETC", "ILV", "HEI", "PAXG", "ZRO", "KSM", "BCH", "AVAX", "DOT", "HYPER", "IMX", "IOST", "PENDLE", "DOGE", "WOO",
        //     "VELODROME", "AGLD", "ROSE", "ACE", "NEAR", "ME", "DIA", "PORTO", "RIF", "ZIL", "SCR", "NEO", "RAY", "ONDO",
        //     "OP", "PYR", "VET", "THE", "SFP", "ENS", "BTC", "STX", "KAVA", "QTUM", "SUN", "BLUR", "SXP", "CRV", "LRC", "XRP",
        //     "WBTC", "ANKR", "ACM", "EIGEN", "QUICK", "NEXO", "REZ", "GMT", "ASTR", "FORTH", "ACX", "ADA", "CTK", "RONIN",
        //     "RARE", "XAI", "SUSHI", "BAND", "VANA", "TUT", "AWE", "BNT", "GALA", "ONG", "CITY", "AEVO", "ORDI", "BMT", "RLC",
        //     "TST", "IOTX", "HBAR", "COMP", "AXS", "ORCA", "KERNEL", "RAD", "XVS", "USTC", "CELR", "MAGIC", "OMNI", "UMA",
        //     "MANTA", "AI", "DGB", "SNX", "MANA", "LAZIO", "OG", "RENDER", "PUNDIX", "CAKE", "ICP", "BNB", "ARK", "AAVE",
        //     "SUI", "IOTA", "APT", "LINK", "STG", "ZRX", "ALICE", "JUV", "TIA", "PARTI", "AXL", "CFX", "PSG", "KNC", "MTL",
        //     "YFI", "CTSI", "TWT", "LTC", "AMP", "PHB", "GRT", "ARPA", "MASK", "DCR", "BIGTIME", "MAV", "HOME", "XEC", "BEAMX",
        //     "WLD", "CETUS", "STRK", "VTHO", "LUNC", "VANRY", "SOLV", "INJ", "PEOPLE", "ID", "DASH", "ONT", "METIS", "ACA",
        //     "ETHFI", "HAEDAL", "KDA", "MINA", "CGPT", "TRX", "BIFI", "SPELL", "JST", "RVN", "NEIRO", "PERP", "ARDR", "COS",
        //     "BNSOL", "ACT", "BANANA", "T", "1000SATS", "MEME", "GHST", "FTT", "BROCCOLI714", "CHZ", "LAYER", "TRUMP", "SOL",
        //     "LUNA", "OSMO", "DF", "TAO", "FIDA", "BOME", "S", "KAIA", "ARKM", "THETA", "SHELL", "GAS", "BERA", "AR", "LISTA",
        //     "FORM", "ALT", "JASMY", "WIF", "NOT", "NIL", "TON", "ZEN", "BANANAS31", "OM", "QNT", "ENA", "TURBO", "SAHARA",
        //     "JOE", "COTI", "NXPC", "PHA", "IO", "AUCTION", "W", "SOPH", "RESOLV", "LPT", "LQTY", "PORTAL", "PYTH", "CHESS",
        //     "KMNO", "ANIME", "MOVE", "JUP", "PNUT", "JTO", "1000CHEEMS", "BB", "BONK", "RSR", "EDU", "APE", "ACH", "LDO",
        //     "SXT", "SPK", "SANTOS", "HUMA", "NMR", "STRAX", "HMSTR", "HFT", "1000CAT", "DOGS", "SEI", "SIGN", "COOKIE", "GPS",
        //     "BABY", "MUBARAK", "RED", "DATA", "EPIC", "VIRTUAL", "FUN", "KAITO", "AIXBT", "BIO", "NEWT", "PROM", "ALPINE",
        //     "BAKE", "SYRUP", "GUN", "INIT"
        // ];

       $symbols = [
            "FISUSDT", "MDTUSDT", "VICUSDT", "REIUSDT", "LOKAUSDT", "PIVXUSDT", "VOXELUSDT", "MBOXUSDT", "COWUSDT", "FIOUSDT",
            "BELUSDT", "NKNUSDT", "1MBABYDOGEUSDT", "ASRUSDT", "ADXUSDT", "GTCUSDT", "SCRTUSDT", "CVCUSDT", "SYSUSDT", "CYBERUSDT",
            "IDEXUSDT", "RPLUSDT", "AUSDT", "HOOKUSDT", "DEGOUSDT", "NTRNUSDT", "CVXUSDT", "ATAUSDT", "CELOUSDT", "DUSDT", "DUSKUSDT",
            "CKBUSDT", "HIFIUSDT", "UNIUSDT", "GLMRUSDT", "LSKUSDT", "SCUSDT", "1INCHUSDT", "PONDUSDT", "GLMUSDT", "SYNUSDT",
            "SUPERUSDT", "QKCUSDT", "BARUSDT", "TNSRUSDT", "UTKUSDT", "MOVRUSDT", "POLUSDT", "SLFUSDT", "FLUXUSDT", "STOUSDT",
            "FETUSDT", "HIVEUSDT", "ALCXUSDT", "FARMUSDT", "ZECUSDT", "GUSDT", "OXTUSDT", "WANUSDT", "BTTCUSDT", "SHIBUSDT",
            "POLYXUSDT", "YGGUSDT", "NFPUSDT", "WAXPUSDT", "ARBUSDT", "FLOKIUSDT", "DODOUSDT", "BICOUSDT", "API3USDT", "ONEUSDT",
            "MLNUSDT", "BATUSDT", "SKLUSDT", "XLMUSDT", "TFUELUSDT", "USUALUSDT", "DENTUSDT", "TLMUSDT", "HIGHUSDT", "STEEMUSDT",
            "SAGAUSDT", "DYDXUSDT", "SLPUSDT", "PIXELUSDT", "DEXEUSDT", "SSVUSDT", "AUDIOUSDT", "XNOUSDT", "XTZUSDT", "WINUSDT",
            "POWRUSDT", "TRBUSDT", "FILUSDT", "IQUSDT", "ETHUSDT", "WBETHUSDT", "RUNEUSDT", "FXSUSDT", "GNSUSDT", "LUMIAUSDT",
            "TKOUSDT", "ATMUSDT", "GNOUSDT", "PENGUUSDT", "DYMUSDT", "REQUSDT", "RDNTUSDT", "TRUUSDT", "OGNUSDT", "AVAUSDT",
            "CATIUSDT", "FLMUSDT", "EGLDUSDT", "XVGUSDT", "ICXUSDT", "GMXUSDT", "C98USDT", "STORJUSDT", "QIUSDT", "ZKUSDT",
            "WCTUSDT", "SANDUSDT", "MKRUSDT", "CHUSDT", "ALGOUSDT", "ATOMUSDT", "MBLUSDT", "ENJUSDT", "FLOWUSDT", "PEPEUSDT",
            "HOTUSDT", "ETCUSDT", "ILVUSDT", "HEIUSDT", "PAXGUSDT", "ZROUSDT", "KSMUSDT", "BCHUSDT", "AVAXUSDT", "DOTUSDT",
            "HYPERUSDT", "IMXUSDT", "IOSTUSDT", "PENDLEUSDT", "DOGEUSDT", "WOOUSDT", "VELODROMEUSDT", "AGLDUSDT", "ROSEUSDT",
            "ACEUSDT", "NEARUSDT", "MEUSDT", "DIAUSDT", "PORTOUSDT", "RIFUSDT", "ZILUSDT", "SCRUSDT", "NEOUSDT", "RAYUSDT",
            "ONDOUSDT", "OPUSDT", "PYRUSDT", "VETUSDT", "THEUSDT", "SFPUSDT", "ENSUSDT", "BTCUSDT", "STXUSDT", "KAVAUSDT",
            "QTUMUSDT", "SUNUSDT", "BLURUSDT", "SXPUSDT", "CRVUSDT", "LRCUSDT", "XRPUSDT", "WBTCUSDT", "ANKRUSDT", "ACMUSDT",
            "EIGENUSDT", "QUICKUSDT", "NEXOUSDT", "REZUSDT", "GMTUSDT", "ASTRUSDT", "FORTHUSDT", "ACXUSDT", "ADAUSDT", "CTKUSDT",
            "RONINUSDT", "RAREUSDT", "XAIUSDT", "SUSHIUSDT", "BANDUSDT", "VANAUSDT", "TUTUSDT", "AWEUSDT", "BNTUSDT", "GALAUSDT",
            "ONGUSDT", "CITYUSDT", "AEVOUSDT", "ORDIUSDT", "BMTUSDT", "RLCUSDT", "TSTUSDT", "IOTXUSDT", "HBARUSDT", "COMPUSDT",
            "AXSUSDT", "ORCAUSDT", "KERNELUSDT", "RADUSDT", "XVSUSDT", "USTCUSDT", "CELRUSDT", "MAGICUSDT", "OMNIUSDT", "UMAUSDT",
            "MANTAUSDT", "AIUSDT", "DGBUSDT", "SNXUSDT", "MANAUSDT", "LAZIOUSDT", "OGUSDT", "RENDERUSDT", "PUNDIXUSDT", "CAKEUSDT",
            "ICPUSDT", "BNBUSDT", "ARKUSDT", "AAVEUSDT", "SUIUSDT", "IOTAUSDT", "APTUSDT", "LINKUSDT", "STGUSDT", "ZRXUSDT",
            "ALICEUSDT", "JUVUSDT", "TIAUSDT", "PARTIUSDT", "AXLUSDT", "CFXUSDT", "PSGUSDT", "KNCUSDT", "MTLUSDT", "YFIUSDT",
            "CTSIUSDT", "TWTUSDT", "LTCUSDT", "AMPUSDT", "PHBUSDT", "GRTUSDT", "ARPAUSDT", "MASKUSDT", "DCRUSDT", "BIGTIMEUSDT",
            "MAVUSDT", "HOMEUSDT", "XECUSDT", "BEAMXUSDT", "WLDUSDT", "CETUSUSDT", "STRKUSDT", "VTHOUSDT", "LUNCUSDT", "VANRYUSDT",
            "SOLVUSDT", "INJUSDT", "PEOPLEUSDT", "IDUSDT", "DASHUSDT", "ONTUSDT", "METISUSDT", "ACAUSDT", "ETHFIUSDT", "HAEDALUSDT",
            "KDAUSDT", "MINAUSDT", "CGPTUSDT", "TRXUSDT", "BIFIUSDT", "SPELLUSDT", "JSTUSDT", "RVNUSDT", "NEIROUSDT", "PERPUSDT",
            "ARDRUSDT", "COSUSDT", "BNSOLUSDT", "ACTUSDT", "BANANAUSDT", "TUSDT", "1000SATSUSDT", "MEMEUSDT", "GHSTUSDT", "FTTUSDT",
            "BROCCOLI714USDT", "CHZUSDT", "LAYERUSDT", "TRUMPUSDT", "SOLUSDT", "LUNAUSDT", "OSMOUSDT", "DFUSDT", "TAOUSDT",
            "FIDAUSDT", "BOMEUSDT", "SUSDT", "KAIAUSDT", "ARKMUSDT", "THETAUSDT", "SHELLUSDT", "GASUSDT", "BERAUSDT", "ARUSDT",
            "LISTAUSDT", "FORMUSDT", "ALTUSDT", "JASMYUSDT", "WIFUSDT", "NOTUSDT", "NILUSDT", "TONUSDT", "ZENUSDT", "BANANAS31USDT",
            "OMUSDT", "QNTUSDT", "ENAUSDT", "TURBOUSDT", "SAHARAUSDT", "JOEUSDT", "COTIUSDT", "NXPCUSDT", "PHAUSDT", "IOUSDT",
            "AUCTIONUSDT", "WUSDT", "SOPHUSDT", "RESOLVUSDT", "LPTUSDT", "LQTYUSDT", "PORTALUSDT", "PYTHUSDT", "CHESSUSDT",
            "KMNOUSDT", "ANIMEUSDT", "MOVEUSDT", "JUPUSDT", "PNUTUSDT", "JTOUSDT", "1000CHEEMSUSDT", "BBUSDT", "BONKUSDT",
            "RSRUSDT", "EDUUSDT", "APEUSDT", "ACHUSDT", "LDOUSDT", "SXTUSDT", "SPKUSDT", "SANTOSUSDT", "HUMAUSDT", "NMRUSDT",
            "STRAXUSDT", "HMSTRUSDT", "HFTUSDT", "1000CATUSDT", "DOGSUSDT", "SEIUSDT", "SIGNUSDT", "COOKIEUSDT", "GPSUSDT",
            "BABYUSDT", "MUBARAKUSDT", "REDUSDT", "DATAUSDT", "EPICUSDT", "VIRTUALUSDT", "FUNUSDT", "KAITOUSDT", "AIXBTUSDT",
            "BIOUSDT", "NEWTUSDT", "PROMUSDT", "ALPINEUSDT", "BAKEUSDT", "SYRUPUSDT", "GUNUSDT", "INITUSDT"
        ];



        // Step 1: Get all 24hr tickers
        $response = Http::get('https://api.binance.com/api/v3/ticker/24hr');

        if (! $response->ok()) {
            $this->error('❌ Failed to fetch 24hr ticker data');
            return;
        }

        $tickers = $response->json();

        // Step 2: Filter only USDT symbols
        $usdtSymbols = array_filter($tickers, function ($ticker) {
            return str_ends_with($ticker['symbol'], 'USDT');
        });

        // Step 3: Sort by priceChangePercent ASC (Top Losers first)
        usort($usdtSymbols, function ($a, $b) {
            return $a['priceChangePercent'] <=> $b['priceChangePercent'];
        });

        // Step 4: Get top 10 or top 50 losers (your choice)
        $topLosers = array_slice($usdtSymbols, 0, 50); // top 50 losers

        // Step 5: Now loop only on the top loser symbols
        $interval = '15m';

        foreach ($topLosers as $loser) {
            $symbol = $loser['symbol'];
            $this->info("🔍 Checking Top Loser Symbol: $symbol");

            $response = Http::get("https://api.binance.com/api/v3/klines", [
                'symbol' => $symbol,
                'interval' => $interval,
                'limit' => 73
            ]);

            if (!$response->ok()) {
                $this->error("❌ Failed to fetch data for $symbol");
                continue;
            }

            $candles = $response->json();

            for ($index = 5; $index < count($candles); $index++) {
                $prev = $candles[$index - 1];
                $current = $candles[$index];

                $prevOpen = (float) $prev[1];
                $prevClose = (float) $prev[4];
                $currOpen = (float) $current[1];
                $currClose = (float) $current[4];

                // ✅ Condition 1: Engulfing pattern
                $isEngulfing = (
                    $prevOpen > $prevClose &&
                    $currClose > $currOpen &&
                    abs($prevClose - $currOpen) < 0.000001 &&
                    $currClose > $prevOpen
                );

                if (!$isEngulfing) continue;

                // ✅ Condition 2: Spot check
                $isSpot = true;
                for ($j = $index - 5; $j < $index - 1; $j++) {
                    $priorOpen = (float) $candles[$j][1];
                    $priorClose = (float) $candles[$j][4];

                    if ($currOpen >= $priorOpen || $currOpen >= $priorClose) {
                        $isSpot = false;
                        break;
                    }
                }

                if (!$isSpot) continue;

                $openTime = Carbon::createFromTimestampMs($current[0])->setTimezone('Asia/Karachi');

                if (Candle::where('symbol', $symbol)->where('open_time', $openTime)->exists()) {
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
                    'is_bullish_engulfing' => true
                ]);

                $this->info("✅ [$symbol] Engulfing Spot Candle at: $openTime | O: $currOpen C: $currClose");
            }
        }

        $this->info("🎯 Finished scanning all symbols.");
    }
}
