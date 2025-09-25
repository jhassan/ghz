<?php

namespace App\Http\Controllers;

use App\Models\Coins;
use Illuminate\Http\Request;

class CoinsController extends Controller
{
    public function edit()
    {
        $setting = Coins::first(); // or find(1) if only one row
        $coins = $setting?->coins ?? '';

        // Convert array to comma-separated string for textarea
        // $coinString = implode(',', $coins);
        $coinString = $coins;

        return view('coins.edit', compact('coinString'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'coins' => 'required|string',
        ]);

        // If admin enters one coin per line
        // $coins = preg_split('/\r\n|\r|\n/', trim($request->coins));
        // dd(json_encode($request->coins));
        $coins = $request->coins;

        // Or if comma separated, keep this:
        // $coins = array_map('trim', explode(',', $request->coins));

        // Save properly
        Coins::updateOrCreate(
            ['id' => 1],
            ['coins' => $coins]
        );

        return redirect()->route('coins.edit')->with('success', 'Coins updated successfully!');
    }
}
