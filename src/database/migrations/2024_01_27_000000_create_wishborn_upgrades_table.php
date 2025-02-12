<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishborn_upgrades', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('batch');
            $table->timestamp('executed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishborn_upgrades');
    }
}; 