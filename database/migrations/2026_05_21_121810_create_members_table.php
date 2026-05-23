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
        // Add this line right here to kill conflicts:
        Schema::dropIfExists('members');

        Schema::create('members', function (Blueprint $table) {
    $table->id();
    $table->string('title', 50)->nullable();  // Mr, Mrs, Dr, Pastor, etc.
    $table->string('first_name', 100);
    $table->string('last_name', 100);
    $table->string('email', 255)->unique();
    $table->string('phone', 30)->nullable();
    $table->string('church', 150)->nullable();
    $table->string('cell', 150)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
