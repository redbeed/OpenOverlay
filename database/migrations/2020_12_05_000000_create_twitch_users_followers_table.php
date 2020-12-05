<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitchUserFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_user_followers', function (Blueprint $table) {
            $table->id();
            $table->string('twitch_user_id');

            $table->string('follower_user_id');
            $table->string('follower_username');

            $table->timestamp('followed_at')->nullable();
            $table->timestamps();

            $table->unique('twitch_user_id', 'follower_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_user_followers');
    }
}
