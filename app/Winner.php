<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'check_number', 'chance', 'prize', 'when'
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
