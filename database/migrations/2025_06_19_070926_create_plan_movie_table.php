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
        Schema::create('plan_movie', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')->constrained()->onDelete('cascade');     // FK → plans.id
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // FK → items.id

            $table->unsignedBigInteger('item_created_by')->nullable()->comment('ID of user/admin/vendor who added the item');
            $table->boolean('is_vendor')->default(0)->comment('1 = added by vendor, 0 = added by admin');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_movie');
    }
};
