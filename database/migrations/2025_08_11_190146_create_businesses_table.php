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
        Schema::create('businesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tin_number')->nullable()->index();
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('business_user', function (Blueprint $table) {
            $table->foreignUuid('business_id')->constrained();
            $table->foreignUuid('user_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_user');
        Schema::dropIfExists('businesses');
    }
};
