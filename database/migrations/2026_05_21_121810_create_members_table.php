<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('members');

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 30)->nullable();
            $table->string('email', 255)->nullable()->unique();
            $table->string('group', 150)->nullable();
            $table->string('church', 150)->nullable();
            $table->string('cell', 150)->nullable();
            $table->date('birthday')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
