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
        Schema::create('user_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // FK → users.id
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');  // FK → offers.id

            $table->decimal('price_paid', 10, 2)->default(0.00);                // Final amount paid for the offer

            $table->timestamp('purchased_at')->nullable();                     // When offer was purchased
            $table->timestamp('access_start')->nullable();                     // When access starts (can be = purchased_at)
            $table->timestamp('expired_date')->nullable();                     // Access expires

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_offers');
    }
};
