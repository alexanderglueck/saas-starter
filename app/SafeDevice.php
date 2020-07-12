<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SafeDevice extends Model
{
    protected $fillable = [
        'token', 'ip', 'added_at', 'name'
    ];
}
