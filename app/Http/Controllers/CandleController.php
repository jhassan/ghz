<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candle;

class CandleController extends Controller
{
    public function index(Request $request)
    {
        $symbols = Candle::distinct()->pluck('symbol');
        $query = Candle::query();

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->symbol);
        }

        $candles = $query->orderBy('open_time', 'desc')->paginate(50);

        return view('candles.index', compact('candles', 'symbols'));
    }
}
