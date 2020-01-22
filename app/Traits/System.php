<?php

namespace App\Traits;

use App\Participant;
use GuzzleHttp\Client;

trait System
{
    public $client;

    /**
     * System constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $checkNumber
     * @return bool
     */
    protected function check($checkNumber)
    {
        $checkNumber = Participant::whereCheckNumber($checkNumber)->first();

        if (!$checkNumber) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return string
     */
    public function generate($id)
    {
        if ($id < 10) {
            return '00000' . $id;
        }
        if ($id > 9 && $id < 100) {
            return '0000' . $id;
        }
        if ($id > 99 && $id < 1000) {
            return '000' . $id;
        }
        if ($id > 999 && $id < 10000) {
            return '00' . $id;
        }
        if ($id > 9999 && $id < 100000) {
            return '0' . $id;
        }
    }

    /**
     * @param $user
     * @return bool
     */
    public function sendApprove($user)
    {
        try {
            $chances = '';
            foreach ($user->chances as $chance) {
               $chances .= $chance->chance . ' ';
            }

            $message = 'Поздравляем Вас! Вы успешно зарегистрировались для участия в рекламной игре. Ваши шансы: ' . $chances;
            $phone = str_replace('-', '', str_replace(' ', '', $user->phone));
            $this->client->get('https://userarea.sms-assistent.by/api/v1/send_sms/plain?user='
                . env('SMS_ASSISTENT_USERNAME'). '&password='
                . env('SMS_ASSISTENT_PASSWORD')
                . '&recipient=' . $phone
                . '&message=' . $message
                . '&sender=' . env('SMS_ASSISTENT_SENDER'));
            return true;
        } catch (\Exception $exception) {
            return false;
        }

    }

    /**
     * @param $user
     * @param $reason
     * @return bool
     */
    public function sendDecline($user, $reason)
    {
        try {
            $phone = str_replace('-', '', str_replace(' ', '', $user->phone));
            $this->client->get('https://userarea.sms-assistent.by/api/v1/send_sms/plain?user='
                . env('SMS_ASSISTENT_USERNAME'). '&password='
                . env('SMS_ASSISTENT_PASSWORD')
                . '&recipient=' . $phone
                . '&message=' . $reason
                . '&sender=' . env('SMS_ASSISTENT_SENDER'));
            return true;
        } catch (\Exception $exception) {
            return false;
        }

    }

    /**
     * @param $array
     * @return \Illuminate\Support\Collection
     */
    public function convert($array)
    {
        $collection = collect($array);
        $mapped = $collection->map(function($item, $key) {
            $string = new \stdClass();
            $string->id = $item->id;
            $string->chance = '';
            $string->name = $item->name;
            $string->surname = $item->surname;
            $string->secondName = $item->secondName;
            $string->check_number = $item->check_number;
            $string->date = $item->date;
            $string->phone = $item->phone;
            $string->email = $item->email;
            $string->address = $item->address;
            $string->image = $item->image;
            $string->fromTelegram = $item->fromTelegram;
            $string->fromVk = $item->fromVk;
            $string->check_number = $item->check_number;
            $string->created_at = $item->created_at;
            $string->updated_at = $item->updated_at;
            foreach ($item->chances as $value) {
                $string->chance .= $value->chance . ' ';
            }
            return $string;
        });

        return $mapped;
    }
}
