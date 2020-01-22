<?php

namespace App\Http\Controllers;

use App\Participant;
use GuzzleHttp\Client;
use Intervention\Image\Facades\Image;

class VKBotController extends Controller
{
    private $response = [];

    public function handle()
    {
        switch (request()->type) {
            case 'confirmation':
                echo env('VK_API_CALLBACK');
                break;
            case 'message_new':
                $user = explode(',', str_replace(' ', '', request()->object['message']['text']));
                $participant = new Participant();
                $participant->storeFromVKBot($user);
                $image = Image::make(request()->object['message']['attachments'][0]['photo']['sizes'][4]['url']);
                $image->encode('jpg', 80)->save('participants/' . $participant->id . '.jpg', 80);
                $participant->image = $participant->id . '.jpg';
                $participant->save();
                $this->sendMessage('Вы зарегистрировались');
                echo 'ok';
                die();
        }

    }

    private function sendMessage($message)
    {
        $client = new Client();
        $client->request('POST', env('VK_API_ENDPOINT')
            . 'messages.send?user_id=' . request()->object['message']['from_id']
            . '&message=' . urldecode($message)
            . '&v=5.103&'
            . '&read_state=1'
            . '&random_id=' . request()->object['message']['random_id']
            . '&access_token=' . env('VK_API_GROUP'));
    }

    private function userInfo()
    {
        $client = new Client();
        $response = $client->request('POST', env('VK_API_ENDPOINT')
            . 'users.get?user_id=' . request()->object['message']['from_id']
            . '&v=5.103&'
            . '&access_token=' . env('VK_API_GROUP'));
        return json_decode($response->getBody());
    }
}
