<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('wallet', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->integer('item_id');
        $table->string('commission_amount');
        $table->string('item_amount');
        $table->integer('status');
        $table->integer('purchased_by');
        $table->string('approved_date')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet');
    }
};
