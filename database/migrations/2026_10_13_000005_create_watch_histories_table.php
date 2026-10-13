<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->restrictOnDelete();
            $table->foreignId('movie_id')->constrained()->restrictOnDelete();
            $table->dateTime('watched_at');
            $table->unsignedSmallInteger('minutes_watched')->default(0);
            $table->boolean('completed')->default(false);
            $table->string('device', 80)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
