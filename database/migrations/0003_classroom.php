<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('age_group')->nullable();
            $table->string('room_number')->nullable();
            $table->integer('capacity')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::create('classroom_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_id');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('activity')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('classroom_id')
                  ->references('id')
                  ->on('classrooms')
                  ->onDelete('cascade');
        });

        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->text('medical_info')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
           $table->foreign('parent_id')
                  ->references('id')
                 ->on('users')
                 ->onDelete('cascade');
            
            $table->foreign('classroom_id')
                  ->references('id')
                  ->on('classrooms')
                  ->onDelete('set null');
        });

        Schema::create('child_parent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->unsignedBigInteger('parent_id');
            $table->timestamps();

            // Composite unique constraint to prevent duplicate relationships
            $table->unique(['child_id', 'parent_id']);

            // Foreign key constraints
            $table->foreign('child_id')
                  ->references('id')
                  ->on('children')
                  ->onDelete('cascade');
            
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });


        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->unsignedBigInteger('uploaded_by');
            $table->string('path');
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('child_id')
                  ->references('id')
                  ->on('children')
                  ->onDelete('cascade');
            
            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_schedules');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('children');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('child_parent');
    }
};