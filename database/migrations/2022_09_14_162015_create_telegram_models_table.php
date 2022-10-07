<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_models', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'callback_query',
                'message'
            ]);
            $table->string('from_id')->nullable();
            $table->string('message_id')->default(0);
            $table->string('chat_instance')->nullable();
            $table->string('data')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->string('first_name', 40)->nullable();
            $table->string('username', 40)->nullable();
            $table->string('language_code', 4)->nullable();
            $table->string('chat_id')->nullable();
            $table->string('chat_type', 30)->nullable();
            $table->string('unix_timestamp')->nullable();
            $table->string('text')->nullable();
            $table->json('telegram')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('finish')->default(false);
            $table->enum('reminder_type', [
                'front',
                'backend',
                'body',
                'additional_text',
            ])->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('telegram_models');
    }
};
