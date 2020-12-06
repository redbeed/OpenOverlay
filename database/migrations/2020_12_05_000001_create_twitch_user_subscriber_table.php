<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitchUserSubscriberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_user_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('twitch_user_id');

            $table->string('subscriber_user_id');
            $table->string('subscriber_username');

            $table->boolean('is_gift');

            $table->string('tier');
            $table->string('tier_name');

            $table->timestamps();

            $table->unique(['twitch_user_id', 'subscriber_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_user_subscribers');
    }
}
