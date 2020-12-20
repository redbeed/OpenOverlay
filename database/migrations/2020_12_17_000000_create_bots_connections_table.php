<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotsConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bots_connections', function (Blueprint $table) {
            $table->id();
            $table->string('service', 50)->default('twitch');

            $table->bigInteger('bot_user_id')->nullable();
            $table->string('bot_username')->nullable();

            $table->text('service_token');
            $table->text('service_refresh_token')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['bot_user_id', 'service']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bots_connections');
    }
}
