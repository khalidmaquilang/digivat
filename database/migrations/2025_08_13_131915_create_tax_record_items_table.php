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
        Schema::create('tax_record_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tax_record_id', 60);
            $table->string('item_name');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('discount_amount');
            $table->integer('total');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tax_record_id')
                ->references('id')
                ->on('tax_records')
                ->cascadeOnDelete();

            $table->index('tax_record_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_record_items', function (Blueprint $table) {
            $table->dropForeign(['tax_record_id']);
        });
        Schema::dropIfExists('tax_record_items');
    }
};
