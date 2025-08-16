<?php

use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
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
        Schema::create('tax_records', function (Blueprint $table) {
            $table->string('id', 60)->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('sales_date')->nullable();
            $table->string('transaction_reference');
            $table->integer('gross_amount');
            $table->integer('order_discount')->default(0);
            $table->integer('taxable_amount');
            $table->integer('tax_amount');
            $table->integer('total_amount');
            $table->date('valid_until');
            $table->enum('status', TaxRecordStatusEnum::toArray());
            $table->enum('category_type', CategoryTypeEnum::toArray());
            $table->string('referer')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_records');
    }
};
