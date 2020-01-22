<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('name');
            $table->longText('surname');
            $table->longText('secondName');
            $table->string('check_number', 256);
            $table->string('image', 512)->default('');
            $table->string('phone', 20);
            $table->string('email', 256);
            $table->integer('accepted')->default(0);
            $table->longText('address');
            $table->string('date', 256);
            $table->boolean('fromTelegram')->default(false);
            $table->boolean('fromVk')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
}
