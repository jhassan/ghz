<?php

// app/Models/Candle.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candle extends Model
{
    protected $fillable = [
        'symbol', 'interval', 'open_time', 'open', 'close', 'high', 'low', 'is_bullish_engulfing'
    ];

    protected $casts = [
        'open_time' => 'datetime',
        'is_bullish_engulfing' => 'boolean'
    ];
}
