<?php

use Features\UserProfile\Enums\AddressTypeEnum;
use Features\UserProfile\Enums\BirInvoiceMannerTypeEnum;
use Features\UserProfile\Enums\BirInvoiceTypeEnum;
use Features\UserProfile\Enums\BusinessClassificationEnum;
use Features\UserProfile\Enums\CivilStatusEnum;
use Features\UserProfile\Enums\FacilityTypeEnum;
use Features\UserProfile\Enums\GenderEnum;
use Features\UserProfile\Enums\MultipleEmploymentTypeEnum;
use Features\UserProfile\Enums\RegistrationAccreditationTaxRegimeEnum;
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
            $table->string('business_number')->nullable();
            $table->json('industries')->nullable();
            $table->string('incentives_investment_promotion')->nullable();
            $table->string('incentives_legal_basis')->nullable();
            $table->string('incentives_granted')->nullable();
            $table->integer('incentives_number_of_years')->nullable();
            $table->date('incentives_start_date')->nullable();
            $table->date('incentives_end_date')->nullable();
            $table->string('registration_accreditation_number')->nullable();
            $table->date('registration_accreditation_effective_date_from')->nullable();
            $table->date('registration_accreditation_effective_date_to')->nullable();
            $table->date('registration_accreditation_date_issued')->nullable();
            $table->string('registration_accreditation_registered_activity')->nullable();
            $table->enum('registration_accreditation_tax_regime', RegistrationAccreditationTaxRegimeEnum::toArray())->nullable();
            $table->date('registration_accreditation_activity_start_date')->nullable();
            $table->date('registration_accreditation_activity_end_date')->nullable();
            // Part 6 – Facility Details
            $table->string('facility_code')->nullable();
            $table->enum('facility_type', FacilityTypeEnum::toArray())->nullable();
            $table->string('facility_others')->nullable();
            $table->string('facility_unit')->nullable();
            $table->string('facility_building')->nullable();
            $table->string('facility_block')->nullable();
            $table->string('facility_street')->nullable();
            $table->string('facility_street')->nullable();
            $table->string('facility_subdivision')->nullable();
            $table->string('facility_barangay')->nullable();
            $table->string('facility_district')->nullable();
            $table->string('facility_city')->nullable();
            $table->string('facility_province')->nullable();
            $table->string('facility_zip_code')->nullable();
            // Part 7 – Tax Types
            $table->json('tax_types')->nullable();
            // Part 8 – Invoices
            $table->boolean('use_bir_invoice')->nullable();
            $table->enum('bir_invoice_type', BirInvoiceTypeEnum::toArray())->nullable();
            $table->integer('number_of_booklets')->nullable();
            $table->string('serial_number_start')->nullable();
            $table->string('serial_number_end')->nullable();
            $table->string('printers_name')->nullable();
            $table->string('printers_tin_number')->nullable();
            $table->string('printers_accreditation_number')->nullable();
            $table->date('printers_accreditation_date')->nullable();
            $table->string('printers_unit')->nullable();
            $table->string('printers_building')->nullable();
            $table->string('printers_block')->nullable();
            $table->string('printers_street')->nullable();
            $table->string('printers_street')->nullable();
            $table->string('printers_subdivision')->nullable();
            $table->string('printers_barangay')->nullable();
            $table->string('printers_district')->nullable();
            $table->string('printers_city')->nullable();
            $table->string('printers_province')->nullable();
            $table->string('printers_zip_code')->nullable();
            $table->string('printers_phone')->nullable();
            $table->string('printers_email')->nullable();
            $table->enum('invoice_manner_type', BirInvoiceMannerTypeEnum::toArray())->nullable();
            $table->json('invoice_descriptions')->nullable();
            // Part 9 – For Employee with Two or More Employers (Multiple Employments) Within the Calendar Year
            $table->enum('multiple_employment_type', MultipleEmploymentTypeEnum::toArray())->nullable();
            $table->json('employers');
            $table->date('primary_employer_relationship_date')->nullable();
            $table->json('primary_employer_contact_type')->nullable();
            $table->string('signature_image')->nullable();

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
