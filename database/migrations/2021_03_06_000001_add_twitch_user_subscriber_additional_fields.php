<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitchUserSubscriberAdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twitch_user_subscribers', function (Blueprint $table) {
            $table->string('subscriber_login_name');

            $table->string('gifter_user_id');
            $table->string('gifter_username');
            $table->string('gifter_login_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twitch_user_subscribers', function (Blueprint $table) {
            $table->dropColumn('subscriber_login_name');
            $table->dropColumn('gifter_user_id');
            $table->dropColumn('gifter_username');
            $table->dropColumn('gifter_login_name');
        });
    }
}
