<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consent_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('consent_given')->default(false);
            $table->timestamp('consent_given_at')->nullable();
            $table->unsignedBigInteger('consent_form_id')->nullable();
            $table->foreign('consent_form_id')->references('id')->on('consent_forms');
        });

    
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_forms');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['consent_form_id']);
            $table->dropColumn(['consent_given', 'consent_given_at', 'consent_form_id']);
        });
    }
};