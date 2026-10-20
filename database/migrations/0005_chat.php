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
        Schema::dropIfExists('chat_messages'); // Drop messages first due to foreign key
        Schema::dropIfExists('chats');

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable(); // Optional chat title
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            // Ensure one chat per teacher-parent pair
            $table->unique(['teacher_id', 'parent_id']);
        });

        // Create messages table
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->json('photos')->nullable(); // Store multiple photo paths as JSON
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages'); 
        Schema::dropIfExists('chats');
    }
};