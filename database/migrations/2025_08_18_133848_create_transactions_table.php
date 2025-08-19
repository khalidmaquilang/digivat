<?php

use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
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
            $table->string('id', 60)->primary();
            $table->foreignUuid('business_id')->constrained()->cascadeOnDelete();
            $table->string('tax_record_id');
            $table->integer('amount');
            $table->string('reference_number');
            $table->string('type')->default(TransactionTypeEnum::TaxRemittance); // tax_remittance, refund, adjustment, etc.
            $table->text('description')->nullable();
            $table->timestamp('transaction_date');
            $table->string('status')->default(TransactionStatusEnum::Completed); // completed, pending, failed, cancelled
            $table->json('metadata')->nullable(); // for additional transaction details
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tax_record_id')
                ->references('id')
                ->on('tax_records')
                ->cascadeOnDelete();
            $table->index(['type']);
            $table->index(['transaction_date']);
            $table->index(['status']);
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
