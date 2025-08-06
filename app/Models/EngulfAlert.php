<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngulfAlert extends Model
{
    protected $fillable = ['symbol', 'type', 'source', 'sequence', 'detected_at'];
    public $timestamps = false;
}
