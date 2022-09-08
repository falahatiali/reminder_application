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
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->string('frontend')->nullable();
            $table->string('backend')->nullable();
            $table->text('body')->nullable();
            $table->text('additional_text')->nullable();
            $table->string('reminder_type')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('frequency', 255);
            $table->integer('day')->nullable();
            $table->integer('date')->nullable();
            $table->string('time')->nullable();
            $table->string('expression');
            $table->boolean('run_once')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reminders');
    }
};
