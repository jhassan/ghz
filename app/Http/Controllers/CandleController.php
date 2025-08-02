<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EngulfAlert;

class CandleController extends Controller
{
    public function index(Request $request)
    {
        $symbols = EngulfAlert::distinct()->pluck('symbol');
        $query = EngulfAlert::query();

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->symbol);
        }

        $candles = $query->orderBy('detected_at', 'desc')->paginate(50);

        return view('candles.index', compact('candles', 'symbols'));
    }
}
