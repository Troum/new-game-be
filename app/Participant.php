<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name',
        'surname',
        'secondName',
        'phone',
        'email',
        'address',
        'date',
        'check_number',
        'accepted',
        'image',
        'fromTelegram',
        'fromVk'

    ];

    /**
     * @param $request
     */
    public function store($request)
    {
        $this->name = $request->name;
        $this->surname = $request->surname;
        $this->secondName = $request->secondName;
        $this->phone = $request->phone;
        $this->email = $request->email;
        $this->address = $request->address;
        $this->date = $request->date;
        $this->check_number = $request->check_number;
        $this->save();
    }

    public function storeFromTelegramBot($response)
    {
        $this->name = $response[0];
        $this->surname = $response[1];
        $this->secondName = $response[2];
        $this->phone = $response[3];
        $this->email = $response[4];
        $this->address = $response[5];
        $this->date = $response[6];
        $this->check_number = $response[7];
        $this->fromTelegram = true;
        $this->save();
    }

    public function storeFromVKBot($response)
    {
        $this->name = $response[1];
        $this->surname = $response[0];
        $this->secondName = $response[2];
        $this->phone = $response[3];
        $this->email = $response[4];
        $this->address = $response[5];
        $this->date = $response[6];
        $this->check_number = $response[7];
        $this->fromVk = true;
        $this->save();
    }

    /**
     * @return HasMany
     */
    public function chances()
    {
        return $this->hasMany(Chance::class, 'participant_id');
    }
}
