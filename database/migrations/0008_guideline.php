<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guidelines', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'facial_recognition'
            $table->text('title');
            $table->text('description');
            $table->json('content'); // Store guidelines as JSON array
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('guidelines');
    }
};