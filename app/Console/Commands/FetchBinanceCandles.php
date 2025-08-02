<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Candle;
use Carbon\Carbon;
use App\Models\EngulfAlert;
use Illuminate\Support\Facades\DB;

class FetchBinanceCandles extends Command
{
    protected $signature = 'binance:fetch {symbols=BTCUSDT,ETHUSDT,NEIROUSDT}';
    protected $description = 'Fetch 15-min candlestick data from Binance and save valid bullish engulfing spot candles for multiple coins';

    public function handle()
    {

          // 🔁 Hardcoded list of coin pairs
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

        $coins = [
            "FIS", "MDT", "VIC", "REI", "LOKA", "PIVX", "VOXEL", "MBOX", "COW", "FIO", "BEL", "NKN", "1MBABYDOGE", "ASR",
            "ADX", "GTC", "SCRT", "CVC", "SYS", "CYBER", "IDEX", "RPL", "A", "HOOK", "DEGO", "NTRN", "CVX", "ATA", "CELO",
            "D", "DUSK", "CKB", "HIFI", "UNI", "GLMR", "LSK", "SC", "1INCH", "POND", "GLM", "SYN", "SUPER", "QKC", "BAR",
            "TNSR", "UTK", "MOVR", "POL", "SLF", "FLUX", "STO", "FET", "HIVE", "ALCX", "FARM", "ZEC", "G", "OXT", "WAN",
            "BTTC", "SHIB", "POLYX", "YGG", "NFP", "WAXP", "ARB", "FLOKI", "DODO", "BICO", "API3", "ONE", "MLN", "BAT",
            "SKL", "XLM", "TFUEL", "USUAL", "DENT", "TLM", "HIGH", "STEEM", "SAGA", "DYDX", "SLP", "PIXEL", "DEXE", "SSV",
            "AUDIO", "XNO", "XTZ", "WIN", "POWR", "TRB", "FIL", "IQ", "ETH", "WBETH", "RUNE", "FXS", "GNS", "LUMIA", "TKO",
            "ATM", "GNO", "PENGU", "DYM", "REQ", "RDNT", "TRU", "OGN", "AVA", "CATI", "FLM", "EGLD", "XVG", "ICX", "GMX",
            "C98", "STORJ", "QI", "ZK", "WCT", "SAND", "MKR", "CHR", "ALGO", "ATOM", "MBL", "ENJ", "FLOW", "PEPE", "HOT",
            "ETC", "ILV", "HEI", "PAXG", "ZRO", "KSM", "BCH", "AVAX", "DOT", "HYPER", "IMX", "IOST", "PENDLE", "DOGE", "WOO",
            "VELODROME", "AGLD", "ROSE", "ACE", "NEAR", "ME", "DIA", "PORTO", "RIF", "ZIL", "SCR", "NEO", "RAY", "ONDO",
            "OP", "PYR", "VET", "THE", "SFP", "ENS", "BTC", "STX", "KAVA", "QTUM", "SUN", "BLUR", "SXP", "CRV", "LRC", "XRP",
            "WBTC", "ANKR", "ACM", "EIGEN", "QUICK", "NEXO", "REZ", "GMT", "ASTR", "FORTH", "ACX", "ADA", "CTK", "RONIN",
            "RARE", "XAI", "SUSHI", "BAND", "VANA", "TUT", "AWE", "BNT", "GALA", "ONG", "CITY", "AEVO", "ORDI", "BMT", "RLC",
            "TST", "IOTX", "HBAR", "COMP", "AXS", "ORCA", "KERNEL", "RAD", "XVS", "USTC", "CELR", "MAGIC", "OMNI", "UMA",
            "MANTA", "AI", "DGB", "SNX", "MANA", "LAZIO", "OG", "RENDER", "PUNDIX", "CAKE", "ICP", "BNB", "ARK", "AAVE",
            "SUI", "IOTA", "APT", "LINK", "STG", "ZRX", "ALICE", "JUV", "TIA", "PARTI", "AXL", "CFX", "PSG", "KNC", "MTL",
            "YFI", "CTSI", "TWT", "LTC", "AMP", "PHB", "GRT", "ARPA", "MASK", "DCR", "BIGTIME", "MAV", "HOME", "XEC", "BEAMX",
            "WLD", "CETUS", "STRK", "VTHO", "LUNC", "VANRY", "SOLV", "INJ", "PEOPLE", "ID", "DASH", "ONT", "METIS", "ACA",
            "ETHFI", "HAEDAL", "KDA", "MINA", "CGPT", "TRX", "BIFI", "SPELL", "JST", "RVN", "NEIRO", "PERP", "ARDR", "COS",
            "BNSOL", "ACT", "BANANA", "T", "1000SATS", "MEME", "GHST", "FTT", "BROCCOLI714", "CHZ", "LAYER", "TRUMP", "SOL",
            "LUNA", "OSMO", "DF", "TAO", "FIDA", "BOME", "S", "KAIA", "ARKM", "THETA", "SHELL", "GAS", "BERA", "AR", "LISTA",
            "FORM", "ALT", "JASMY", "WIF", "NOT", "NIL", "TON", "ZEN", "BANANAS31", "OM", "QNT", "ENA", "TURBO", "SAHARA",
            "JOE", "COTI", "NXPC", "PHA", "IO", "AUCTION", "W", "SOPH", "RESOLV", "LPT", "LQTY", "PORTAL", "PYTH", "CHESS",
            "KMNO", "ANIME", "MOVE", "JUP", "PNUT", "JTO", "1000CHEEMS", "BB", "BONK", "RSR", "EDU", "APE", "ACH", "LDO",
            "SXT", "SPK", "SANTOS", "HUMA", "NMR", "STRAX", "HMSTR", "HFT", "1000CAT", "DOGS", "SEI", "SIGN", "COOKIE", "GPS",
            "BABY", "MUBARAK", "RED", "DATA", "EPIC", "VIRTUAL", "FUN", "KAITO", "AIXBT", "BIO", "NEWT", "PROM", "ALPINE",
            "BAKE", "SYRUP", "GUN", "INIT"
        ];

        $this->checkBullishEngulfings();
        $this->checkBTCDominanceEngulf();


        // $interval = "15m";
        // $max_gap_minutes = 180; // 3 hours
        // $engulf_list = [];

        // foreach ($coins as $coin) {
        //     $candles = $this->fetchCandle($coin, $interval);
        //     if (!$candles || count($candles) < 2) continue;

        //     $prev = $candles[0];
        //     $curr = $candles[1];

        //     if ($this->isEngulfing($prev, $curr)) {
        //         $now = time();
        //         $engulf_key = $coin;

        //         if (!isset($engulf_list[$engulf_key])) {
        //             // First engulf — wait for 15min close (mocked with current close time)
        //             $closeTime = intval($curr[6] / 1000);
        //             if ($now >= $closeTime) {
        //                 $engulf_list[$engulf_key] = ["last" => $now, "count" => 2];
        //                 $influence = $this->detectInfluence($coin);
        //                 echo "$coin | Engulf #2 | $influence\n";
        //             } else {
        //                 // Do not notify yet — waiting for 15m close
        //                 echo "Do not notify yet — waiting for 15m close";
        //                 continue;
        //             }
        //         } else {
        //             $lastTime = $engulf_list[$engulf_key]["last"];
        //             $diff = ($now - $lastTime) / 60;
        //             if ($diff <= $max_gap_minutes) {
        //                 $engulf_list[$engulf_key]["count"]++;
        //                 $engulf_list[$engulf_key]["last"] = $now;
        //                 $engulfNo = $engulf_list[$engulf_key]["count"];
        //                 $influence = detectInfluence($coin);
        //                 echo "$coin | Engulf #$engulfNo | $influence\n";
        //             } else {
        //                 // Reset on 3hr gap
        //                 $engulf_list[$engulf_key] = ["last" => $now, "count" => 2];
        //                 $influence = detectInfluence($coin);
        //                 echo "$coin | Engulf #2 | $influence\n";
        //             }
        //         }
        //     }
        //     // Else: no valid engulf
        // }


        // $this->info("🎯 Finished scanning all symbols.");
    }

    public function getAllSpotSymbols() {
        // $exchangeInfo = json_decode(file_get_contents("https://api.binance.com/api/v3/exchangeInfo"), true);
        // $symbols = [];
        // foreach ($exchangeInfo['symbols'] as $s) {
        //     if ($s['quoteAsset'] === 'USDT' && $s['status'] === 'TRADING' && $s['isSpotTradingAllowed']) {
        //         $symbols[] = $s['symbol'];
        //     }
        // }
        // $symbols = [
        //     "FISUSDT", "MDTUSDT", "VICUSDT", "REIUSDT", "LOKAUSDT", "PIVXUSDT", "VOXELUSDT", "MBOXUSDT", "COWUSDT", "FIOUSDT","BELUSDT", "NKNUSDT", "1MBABYDOGEUSDT", "ASRUSDT", "ADXUSDT", "GTCUSDT", "SCRTUSDT", "CVCUSDT", "SYSUSDT", "CYBERUSDT",
        //     "IDEXUSDT", "RPLUSDT", "AUSDT", "HOOKUSDT", "DEGOUSDT", "NTRNUSDT", "CVXUSDT", "ATAUSDT", "CELOUSDT", "DUSDT", "DUSKUSDT",
        //     "CKBUSDT", "HIFIUSDT", "UNIUSDT", "GLMRUSDT", "LSKUSDT", "SCUSDT", "1INCHUSDT", "PONDUSDT", "GLMUSDT", "SYNUSDT",
        //     "SUPERUSDT", "QKCUSDT", "BARUSDT", "TNSRUSDT", "UTKUSDT", "MOVRUSDT", "POLUSDT", "SLFUSDT", "FLUXUSDT", "STOUSDT",
        //     "FETUSDT", "HIVEUSDT", "ALCXUSDT", "FARMUSDT", "ZECUSDT", "GUSDT", "OXTUSDT", "WANUSDT", "BTTCUSDT", "SHIBUSDT",
        //     "POLYXUSDT", "YGGUSDT", "NFPUSDT", "WAXPUSDT", "ARBUSDT", "FLOKIUSDT", "DODOUSDT", "BICOUSDT", "API3USDT", "ONEUSDT",
        //     "MLNUSDT", "BATUSDT", "SKLUSDT", "XLMUSDT", "TFUELUSDT", "USUALUSDT", "DENTUSDT", "TLMUSDT", "HIGHUSDT", "STEEMUSDT",
        //     "SAGAUSDT", "DYDXUSDT", "SLPUSDT", "PIXELUSDT", "DEXEUSDT", "SSVUSDT", "AUDIOUSDT", "XNOUSDT", "XTZUSDT", "WINUSDT",
        //     "POWRUSDT", "TRBUSDT", "FILUSDT", "IQUSDT", "ETHUSDT", "WBETHUSDT", "RUNEUSDT", "FXSUSDT", "GNSUSDT", "LUMIAUSDT","TKOUSDT", "ATMUSDT", "GNOUSDT", "PENGUUSDT", "DYMUSDT", "REQUSDT", "RDNTUSDT", "TRUUSDT", "OGNUSDT", "AVAUSDT",
        //     "CATIUSDT", "FLMUSDT", "EGLDUSDT", "XVGUSDT", "ICXUSDT", "GMXUSDT", "C98USDT", "STORJUSDT", "QIUSDT", "ZKUSDT",
        //     "WCTUSDT", "SANDUSDT", "MKRUSDT", "CHUSDT", "ALGOUSDT", "ATOMUSDT", "MBLUSDT", "ENJUSDT", "FLOWUSDT", "PEPEUSDT",
        //     "HOTUSDT", "ETCUSDT", "ILVUSDT", "HEIUSDT", "PAXGUSDT", "ZROUSDT", "KSMUSDT", "BCHUSDT", "AVAXUSDT", "DOTUSDT",
        //     "HYPERUSDT", "IMXUSDT", "IOSTUSDT", "PENDLEUSDT", "DOGEUSDT", "WOOUSDT", "VELODROMEUSDT", "AGLDUSDT", "ROSEUSDT",
        //     "ACEUSDT", "NEARUSDT", "MEUSDT", "DIAUSDT", "PORTOUSDT", "RIFUSDT", "ZILUSDT", "SCRUSDT", "NEOUSDT", "RAYUSDT",
        //     "ONDOUSDT", "OPUSDT", "PYRUSDT", "VETUSDT", "THEUSDT", "SFPUSDT", "ENSUSDT", "BTCUSDT", "STXUSDT", "KAVAUSDT",
        //     "QTUMUSDT", "SUNUSDT", "BLURUSDT", "SXPUSDT", "CRVUSDT", "LRCUSDT", "XRPUSDT", "WBTCUSDT", "ANKRUSDT", "ACMUSDT",
        //     "EIGENUSDT", "QUICKUSDT", "NEXOUSDT", "REZUSDT", "GMTUSDT", "ASTRUSDT", "FORTHUSDT", "ACXUSDT", "ADAUSDT", "CTKUSDT",
        //     "RONINUSDT", "RAREUSDT", "XAIUSDT", "SUSHIUSDT", "BANDUSDT", "VANAUSDT", "TUTUSDT", "AWEUSDT", "BNTUSDT", "GALAUSDT",
        //     "ONGUSDT", "CITYUSDT", "AEVOUSDT", "ORDIUSDT", "BMTUSDT", "RLCUSDT", "TSTUSDT", "IOTXUSDT", "HBARUSDT", "COMPUSDT",
        //     "AXSUSDT", "ORCAUSDT", "KERNELUSDT", "RADUSDT", "XVSUSDT", "USTCUSDT", "CELRUSDT", "MAGICUSDT", "OMNIUSDT", "UMAUSDT",
        //     "MANTAUSDT", "AIUSDT", "DGBUSDT", "SNXUSDT", "MANAUSDT", "LAZIOUSDT", "OGUSDT", "RENDERUSDT", "PUNDIXUSDT", "CAKEUSDT",
        //     "ICPUSDT", "BNBUSDT", "ARKUSDT", "AAVEUSDT", "SUIUSDT", "IOTAUSDT", "APTUSDT", "LINKUSDT", "STGUSDT", "ZRXUSDT",
        //     "ALICEUSDT", "JUVUSDT", "TIAUSDT", "PARTIUSDT", "AXLUSDT", "CFXUSDT", "PSGUSDT", "KNCUSDT", "MTLUSDT", "YFIUSDT",
        //     "CTSIUSDT", "TWTUSDT", "LTCUSDT", "AMPUSDT", "PHBUSDT", "GRTUSDT", "ARPAUSDT", "MASKUSDT", "DCRUSDT", "BIGTIMEUSDT",
        //     "MAVUSDT", "HOMEUSDT", "XECUSDT", "BEAMXUSDT", "WLDUSDT", "CETUSUSDT", "STRKUSDT", "VTHOUSDT", "LUNCUSDT", "VANRYUSDT",
        //     "SOLVUSDT", "INJUSDT", "PEOPLEUSDT", "IDUSDT", "DASHUSDT", "ONTUSDT", "METISUSDT", "ACAUSDT", "ETHFIUSDT", "HAEDALUSDT",
        //     "KDAUSDT", "MINAUSDT", "CGPTUSDT", "TRXUSDT", "BIFIUSDT", "SPELLUSDT", "JSTUSDT", "RVNUSDT", "NEIROUSDT", "PERPUSDT",
        //     "ARDRUSDT", "COSUSDT", "BNSOLUSDT", "ACTUSDT", "BANANAUSDT", "TUSDT", "1000SATSUSDT", "MEMEUSDT", "GHSTUSDT", "FTTUSDT",
        //     "BROCCOLI714USDT", "CHZUSDT", "LAYERUSDT", "TRUMPUSDT", "SOLUSDT", "LUNAUSDT", "OSMOUSDT", "DFUSDT", "TAOUSDT",
        //     "FIDAUSDT", "BOMEUSDT", "SUSDT", "KAIAUSDT", "ARKMUSDT", "THETAUSDT", "SHELLUSDT", "GASUSDT", "BERAUSDT", "ARUSDT",
        //     "LISTAUSDT", "FORMUSDT", "ALTUSDT", "JASMYUSDT", "WIFUSDT", "NOTUSDT", "NILUSDT", "TONUSDT", "ZENUSDT", "BANANAS31USDT",
        //     "OMUSDT", "QNTUSDT", "ENAUSDT", "TURBOUSDT", "SAHARAUSDT", "JOEUSDT", "COTIUSDT", "NXPCUSDT", "PHAUSDT", "IOUSDT",
        //     "AUCTIONUSDT", "WUSDT", "SOPHUSDT", "RESOLVUSDT", "LPTUSDT", "LQTYUSDT", "PORTALUSDT", "PYTHUSDT", "CHESSUSDT",
        //     "KMNOUSDT", "ANIMEUSDT", "MOVEUSDT", "JUPUSDT", "PNUTUSDT", "JTOUSDT", "1000CHEEMSUSDT", "BBUSDT", "BONKUSDT",
        //     "RSRUSDT", "EDUUSDT", "APEUSDT", "ACHUSDT", "LDOUSDT", "SXTUSDT", "SPKUSDT", "SANTOSUSDT", "HUMAUSDT", "NMRUSDT",
        //     "STRAXUSDT", "HMSTRUSDT", "HFTUSDT", "1000CATUSDT", "DOGSUSDT", "SEIUSDT", "SIGNUSDT", "COOKIEUSDT", "GPSUSDT",
        //     "BABYUSDT", "MUBARAKUSDT", "REDUSDT", "DATAUSDT", "EPICUSDT", "VIRTUALUSDT", "FUNUSDT", "KAITOUSDT", "AIXBTUSDT",
        //     "BIOUSDT", "NEWTUSDT", "PROMUSDT", "ALPINEUSDT", "BAKEUSDT", "SYRUPUSDT", "GUNUSDT", "INITUSDT"];
        $symbols = ["ICPUSDT", "DOTUSDT", "NEARUSDT", "ORDIUSDT", "INJUSDT", "NEIROUSDT", "SOLUSDT", "XRPUSDT", "ADAUSDT"];

        return $symbols;
    }

    public function getTopLosers($symbols) {
        $losers = [];
        foreach ($symbols as $symbol) {
            $url = "https://api.binance.com/api/v3/ticker/24hr?symbol={$symbol}";
            $data = json_decode(@file_get_contents($url), true);
            if (!$data || !isset($data['priceChangePercent'])) continue;
            $losers[$symbol] = floatval($data['priceChangePercent']);
        }
        asort($losers);
        return array_slice(array_keys($losers), 0, 30);
    }

    public function isBullishEngulfing($prev, $curr) {
        $prevOpen = floatval($prev[1]);
        $prevClose = floatval($prev[4]);
        $currOpen = floatval($curr[1]);
        $currClose = floatval($curr[4]);

        $prevBody = abs($prevClose - $prevOpen);
        if ($prevClose > $prevOpen && $currClose > $currOpen && $currOpen < $prevClose && $currClose > $prevOpen && $prevBody >= 0.25 * abs($currClose - $currOpen)) {
            return true;
        }
        return false;
    }

    function getKlines($symbol, $interval = "15m", $limit = 10) {
        $url = "https://api.binance.com/api/v3/klines?symbol={$symbol}&interval={$interval}&limit={$limit}";
        $response = @file_get_contents($url);
        return $response ? json_decode($response, true) : [];
    }


    public function getVolumeSpikeType($symbol, $candles) {
        $volumes = array_column($candles, 5);
        $lastVolume = floatval(end($volumes));
        $avg = array_sum(array_slice($volumes, 0, -1)) / (count($volumes) - 1);
        if ($lastVolume > 1.5 * $avg) return "OWN VOLUME";

        $btcCandles = $this->getKlines("BTCUSDT", "15m", 10);
        if (empty($btcCandles)) return "UNKNOWN";
        $btcLast = $btcCandles[count($btcCandles) - 1];
        $btcPrev = $btcCandles[count($btcCandles) - 2];
        if (floatval($btcLast[4]) > floatval($btcPrev[4])) return "BTC MOVE";
        return "UNKNOWN";
    }

    // public function saveEngulfToDB($conn, $symbol, $type, $source, $seq) {
    //     $stmt = $conn->prepare("INSERT INTO engulf_alerts (symbol, type, source, sequence, detected_at) VALUES (?, ?, ?, ?, NOW())");
    //     $stmt->bind_param("sssi", $symbol, $type, $source, $seq);
    //     $stmt->execute();
    //     $stmt->close();
    // }

    public function saveEngulfToDB($symbol, $type, $source, $seq)
    {
        EngulfAlert::create([
            'symbol'      => $symbol,
            'type'        => $type,
            'source'      => $source,
            'sequence'    => $seq,
            'detected_at' => now(),
        ]);
    }

    // public function wasRecentlyDetected($conn, $symbol, $hours = 3) {
    //     $stmt = $conn->prepare("SELECT detected_at FROM engulf_alerts WHERE symbol = ? AND type = 'BULLISH' ORDER BY detected_at DESC LIMIT 1");
    //     $stmt->bind_param("s", $symbol);
    //     $stmt->execute();
    //     $stmt->bind_result($lastDetected);
    //     if ($stmt->fetch()) {
    //         $lastTime = strtotime($lastDetected);
    //         $stmt->close();
    //         return (time() - $lastTime) < ($hours * 3600) ? $lastTime : false;
    //     }
    //     $stmt->close();
    //     return false;
    // }

    public function wasRecentlyDetected($symbol, $hours = 3)
    {
        $lastDetected = EngulfAlert::where('symbol', $symbol)
            ->where('type', 'BULLISH')
            ->orderByDesc('detected_at')
            ->value('detected_at');

        if ($lastDetected) {
            $lastTime = Carbon::parse($lastDetected)->timestamp;
            return (time() - $lastTime) < ($hours * 3600) ? $lastTime : false;
        }

        return false;
    }

    // public function getEngulfCountSince($conn, $symbol, $sinceTimestamp) {
    //     $stmt = $conn->prepare("SELECT COUNT(*) FROM engulf_alerts WHERE symbol = ? AND type = 'BULLISH' AND detected_at > FROM_UNIXTIME(?)");
    //     $stmt->bind_param("si", $symbol, $sinceTimestamp);
    //     $stmt->execute();
    //     $stmt->bind_result($count);
    //     $stmt->fetch();
    //     $stmt->close();
    //     return $count;
    // }

    public function getEngulfCountSince($symbol, $sinceTimestamp)
    {
        return EngulfAlert::where('symbol', $symbol)
            ->where('type', 'BULLISH')
            ->where('detected_at', '>', date('Y-m-d H:i:s', $sinceTimestamp))
            ->count();
    }

    // public function checkBullishEngulfings($conn) {
    //     $symbols = getTopLosers(getAllSpotSymbols());
    //     foreach ($symbols as $symbol) {
    //         $candles = getKlines($symbol);
    //         if (count($candles) < 2) continue;

    //         $prev = $candles[count($candles) - 2];
    //         $curr = $candles[count($candles) - 1];
    //         if (isBullishEngulfing($prev, $curr)) {
    //             $lastDetected = wasRecentlyDetected($conn, $symbol);
    //             $sequence = $lastDetected ? getEngulfCountSince($conn, $symbol, $lastDetected) + 1 : 0;
    //             if ($sequence == 0) continue; // Skip first engulf after 3hr reset
    //             $source = getVolumeSpikeType($symbol, $candles);
    //             saveEngulfToDB($conn, $symbol, "BULLISH", $source, $sequence);
    //             echo "Bullish Engulf Detected on $symbol | $source | Sequence: $sequence\n";
    //         }
    //     }
    // }

    public function checkBullishEngulfings()
    {
        $symbols = $this->getTopLosers($this->getAllSpotSymbols());
        // dd($symbols);
        foreach ($symbols as $symbol) {
            $candles = $this->getKlines($symbol);
            info('candels', [$candles]);
            if (count($candles) < 2) {
                continue;
            }

            $prev = $candles[count($candles) - 2];
            $curr = $candles[count($candles) - 1];

            if ($this->isBullishEngulfing($prev, $curr)) {
                $lastDetected = $this->wasRecentlyDetected($symbol);
                $sequence = $lastDetected ? $this->getEngulfCountSince($symbol, $lastDetected) + 1 : 0;

                if ($sequence == 0) {
                    continue; // Skip first engulf after 3hr reset
                }

                $source = $this->getVolumeSpikeType($symbol, $candles);

                $this->saveEngulfToDB($symbol, 'BULLISH', $source, $sequence);

                echo "Bullish Engulf Detected on $symbol | $source | Sequence: $sequence\n";
            }
        }
    }


    function isBearishEngulfingDominance($prev, $curr) {
        return $prev[1] < $prev[4] && $curr[1] == $prev[1] && $curr[4] < $prev[1];
    }

    // function checkBTCDominanceEngulf($conn) {
    //     $candles = getKlines("BTCUSDTDOM", "15m", 10);
    //     if (count($candles) < 2) return;
    //     $prev = $candles[count($candles) - 2];
    //     $curr = $candles[count($candles) - 1];

    //     if (isBearishEngulfingDominance($prev, $curr)) {
    //         $stmt = $conn->prepare("SELECT COUNT(*) FROM btc_dom_engulf_log WHERE timestamp = ?");
    //         $stmt->bind_param("s", $curr[0]);
    //         $stmt->execute();
    //         $stmt->bind_result($count);
    //         $stmt->fetch();
    //         $stmt->close();
    //         if ($count == 0) {
    //             $stmt = $conn->prepare("INSERT INTO btc_dom_engulf_log (timestamp, detected_at) VALUES (?, NOW())");
    //             $stmt->bind_param("s", $curr[0]);
    //             $stmt->execute();
    //             $stmt->close();
    //             echo "\n🚨 BTC Dominance Bearish Engulf Detected at candle time: {$curr[0]}\n";
    //         }
    //     }
    // }

    public function checkBTCDominanceEngulf()
    {
        $candles = $this->getKlines("BTCUSDTDOM", "15m", 10);

        if (count($candles) < 2) {
            return;
        }

        $prev = $candles[count($candles) - 2];
        $curr = $candles[count($candles) - 1];
        $timestamp = $curr[0];

        if ($this->isBearishEngulfingDominance($prev, $curr)) {
            $count = DB::table('btc_dom_engulf_log')
                ->where('timestamp', $timestamp)
                ->count();

            if ($count === 0) {
                DB::table('btc_dom_engulf_log')->insert([
                    'timestamp'   => $timestamp,
                    'detected_at' => now(),
                ]);

                echo "\n🚨 BTC Dominance Bearish Engulf Detected at candle time: {$timestamp}\n";
            }
        }
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
