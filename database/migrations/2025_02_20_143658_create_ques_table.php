<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id')->default(0); // Default to 0
            $table->string('text');
            $table->tinyInteger('category')->default(0); // Category stored as 1, 2, 3 4
            $table->timestamps();
    
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }
    

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
