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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->decimal('price',20,2)->nullable();
            $table->string('type')->nullable();
            $table->integer('duration')->nullable();
            $table->string('stripe_product_id')->unique();
            $table->string('stripe_price_id')->unique();
            $table->enum('interval',['trail','month','year'])->default('trail');
            $table->integer('trial_days')->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
