<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'title'
    ];

    public function winners()
    {
        return $this->hasMany(Winner::class, 'result_id');
    }

    public function delete()
    {
        $this->winners()->delete();

        return parent::delete();
    }
}
