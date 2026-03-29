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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->unsignedBigInteger('origin_office_id')->nullable()->index();
            $table->unsignedBigInteger('destination_office_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('name');
            $table->text('note')->nullable();
            $table->decimal('length', 15, 2)->default(0);
            $table->decimal('width', 15, 2)->default(0);
            $table->decimal('height', 15, 2)->default(0);
            $table->decimal('weight', 15, 2)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('value', 15, 2)->default(0);
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->unsignedBigInteger('price_id')->nullable()->index();
            $table->decimal('final_price', 15, 2)->default(0);
            $table->unsignedBigInteger('creator_id')->nullable()->index();
            $table->smallInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
