<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscriber_name');
            $table->string('email')->unique();
            $table->string('plan', 40);
            $table->decimal('monthly_price', 8, 2);
            $table->date('started_at');
            $table->date('expires_at')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
