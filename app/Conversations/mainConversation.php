<?php

namespace App\Conversations;

use App\Interfaces\BotInteface;
use App\Participant;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class mainConversation extends Conversation implements BotInteface
{

    public $response = [];

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->setName();
    }

    public function setName()
    {
        $question = Question::create('Привет, напиши свое имя');
        $this->ask($question, function(Answer $answer) {
           if ($answer->getText() != '') {
               array_push($this->response, $answer->getText());
               $this->setSurname();
           }
        });
    }

    public function setSurname()
    {
        $question = Question::create('Хорошо, а теперь напиши свою фамилию');
        $this->ask($question, function(Answer $answer) {
            if ($answer->getText() != '') {
                array_push($this->response, $answer->getText());
                $this->setSecondName();
            }
        });
    }

    public function setSecondName()
    {
        $question = Question::create('Отлично, напиши теперь своё отчество');
        $this->ask($question, function(Answer $answer) {
            if ($answer->getText() != '') {
                array_push($this->response, $answer->getText());
                $this->setEmail();
            }
        });
    }

    public function setEmail()
    {
        $question = Question::create('Супер, пришли теперь свой e-mail');
        $this->ask($question, function(Answer $answer) {
            if ($answer->getText() != '' && filter_var($answer->getText(), FILTER_VALIDATE_EMAIL)) {
                array_push($this->response, $answer->getText());
                $this->setPhone();
            } else {
                $this->bot->reply('Ну ты чего? Пришли, пожалуйста, e-mail вида example@example.ru/com/by/org/net или любой другой');
                $this->setEmail();
            }
        });
    }

    public function setPhone()
    {
        $question = Question::create('Супер, пришли теперь свой номер телефона в формате +375 YY XXX-XX-XX');
        $this->ask($question, function(Answer $answer) {
            if ($answer->getText() != '' && preg_match('/^((\+375)+\s(29|33|44|25)\s[0-9]{3}-[0-9]{2}-[0-9]{2})/', $answer->getText()) != 0) {
                array_push($this->response, $answer->getText());
                $this->setAddress();
            } else {
                $this->bot->reply('Ну ты чего? Пришли, пожалуйста, телефон в формате +375 YY XXX-XX-XX');
                $this->setPhone();
            }
        });
    }

    public function setAddress()
    {
        $question = Question::create('Хорошо, а теперь напиши свой адрес');
        $this->ask($question, function(Answer $answer) {
            if ($answer->getText() != '') {
                array_push($this->response, $answer->getText());
                $this->setCheckDate();
            }
        });
    }

    public function setCheckDate()
    {
        $question = Question::create('Хорошо, а теперь введи дату покупки в формате ДД.ММ.ГГГ');
        $this->ask($question, function(Answer $answer) {
            if ($answer->getText() != '') {
                array_push($this->response, $answer->getText());
                $this->setCheckNumber();
            }
        });
    }

    public function setCheckNumber()
    {
        $question = Question::create('Хорошо, а теперь введи номер чека');
        $this->ask($question, function(Answer $answer) {
            if ($answer->getText() != '' && $this->check($answer->getText())) {
                array_push($this->response, $answer->getText());
                $this->setImage();
            } else {
                $this->bot->reply('Извини, но человек с таким чеком уже зарегистрирован');
            }
        });
    }

    public function setImage()
    {
        $this->askForImages('Пожалуйста, добавь фото', function ($images) {
            foreach ($images as $image) {
                array_push($this->response, $image->getUrl());
                $this->exit();
            }
        }, function (Answer $answer){
            $this->bot->reply('Ничего не загружено');
        });
    }

    public function exit()
    {
        $participant = new Participant();
        $participant->storeFromTelegramBot($this->response);
        $image = Image::make($this->response[8]);
        $image->encode('jpg', 80)->save('participants/' . $participant->id . '.jpg', 80);
        $participant->image = $participant->id . '.jpg';
        $participant->save();

        $message = OutgoingMessage::create('Спасибо! До встречи! За розыгрышем следи на сайте!');
        $this->bot->reply($message);
        return true;
    }

    protected function check($checkNumber)
    {
        $checkNumber = Participant::whereCheckNumber($checkNumber)->first();

        if (!$checkNumber) {
            return true;
        } else {
            return false;
        }
    }
}
