<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitchEventSubEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_event_sub_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');

            $table->string('event_type');
            $table->string('event_user_id');
            $table->json('event_data');

            $table->timestamp('event_sent')->nullable();
            $table->timestamps();

            $table->unique('event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_event_sub_events');
    }
}
