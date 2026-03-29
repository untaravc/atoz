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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['office']);
            $table->unsignedBigInteger('origin_office_id')->nullable()->index();
            $table->unsignedBigInteger('destination_office_id')->nullable()->index();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};

