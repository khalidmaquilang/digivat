<?php

use Features\UserProfile\Enums\AddressTypeEnum;
use Features\UserProfile\Enums\BusinessClassificationEnum;
use Features\UserProfile\Enums\CivilStatusEnum;
use Features\UserProfile\Enums\GenderEnum;
use Features\UserProfile\Enums\SpouseEmploymentStatusEnum;
use Features\UserProfile\Enums\TaxPayerTypeEnum;
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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            // Part I - Taxpayer Information
            $table->date('registration_date')->index();
            $table->string('philsys_card_number')->index();
            $table->string('rdo_code')->index();
            $table->enum('taxpayer_type', TaxPayerTypeEnum::toArray());
            $table->string('estate_trust_name')->nullable();
            $table->enum('gender', GenderEnum::toArray());
            $table->enum('civil_status', CivilStatusEnum::toArray());
            $table->date('birth_date');
            $table->string('birth_place')->nullable();
            $table->string('mother_maiden_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('other_citizenship')->nullable();
            $table->string('residential_unit')->nullable();
            $table->string('residential_building')->nullable();
            $table->string('residential_block')->nullable();
            $table->string('residential_street')->nullable();
            $table->string('residential_street')->nullable();
            $table->string('residential_subdivision')->nullable();
            $table->string('residential_barangay')->nullable();
            $table->string('residential_district')->nullable();
            $table->string('residential_city')->nullable();
            $table->string('residential_province')->nullable();
            $table->string('residential_zip_code')->nullable();
            $table->string('business_unit')->nullable();
            $table->string('business_building')->nullable();
            $table->string('business_block')->nullable();
            $table->string('business_street')->nullable();
            $table->string('business_street')->nullable();
            $table->string('business_subdivision')->nullable();
            $table->string('business_barangay')->nullable();
            $table->string('business_district')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_province')->nullable();
            $table->string('business_zip_code')->nullable();
            $table->string('foreign_address')->nullable();
            $table->string('municipal_code')->nullable();
            $table->string('purpose_tin_application')->nullable();
            $table->json('identification_details')->nullable();
            $table->json('contact_type')->nullable();
            $table->boolean('is_availing_8_percent_rate')->default(false);
            // Part 2 - Taxpayer Classification
            $table->enum('business_classification', BusinessClassificationEnum::toArray());
            // Part 3 - Spouse Information
            $table->enum('spouse_employment_status', SpouseEmploymentStatusEnum::toArray())->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_middle_name')->nullable();
            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_suffix')->nullable();
            $table->string('spouse_tin_number')->nullable();
            $table->string('spouse_employer_first_name')->nullable();
            $table->string('spouse_employer_middle_name')->nullable();
            $table->string('spouse_employer_last_name')->nullable();
            $table->string('spouse_employer_suffix')->nullable();
            $table->string('spouse_employer_tin_number')->nullable();
            // Part 4 - Authorized Representative
            $table->string('authorized_representative_first_name')->nullable();
            $table->string('authorized_representative_middle_name')->nullable();
            $table->string('authorized_representative_last_name')->nullable();
            $table->string('authorized_representative_suffix')->nullable();
            $table->string('authorized_representative_nickname')->nullable();
            $table->string('non_individual_authorized_representative_name')->nullable();
            $table->date('relationship_date')->nullable();
            $table->enum('address_type', AddressTypeEnum::toArray())->nullable();
            $table->string('authorized_unit')->nullable();
            $table->string('authorized_building')->nullable();
            $table->string('authorized_block')->nullable();
            $table->string('authorized_street')->nullable();
            $table->string('authorized_street')->nullable();
            $table->string('authorized_subdivision')->nullable();
            $table->string('authorized_barangay')->nullable();
            $table->string('authorized_district')->nullable();
            $table->string('authorized_city')->nullable();
            $table->string('authorized_province')->nullable();
            $table->string('authorized_zip_code')->nullable();
            $table->json('authorized_contact_type')->nullable();
            // Part 5 - Business Information

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
