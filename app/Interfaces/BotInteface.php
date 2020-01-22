<?php


namespace App\Interfaces;


interface BotInteface
{
    public function setName();
    public function setSurname();
    public function setSecondName();
    public function setPhone();
    public function setEmail();
    public function setAddress();
    public function setCheckDate();
    public function setCheckNumber();
    public function setImage();
    public function exit();
}
