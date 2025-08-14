<?php

declare(strict_types=1);

namespace Features\TaxRecord\Enums;

use Features\Shared\Enums\EnumArrayTrait;
use Features\TaxType\Interfaces\TaxInterface;
use Features\TaxType\Vat;
use Filament\Support\Contracts\HasLabel;

enum CategoryTypeEnum: string implements HasLabel
{
    use EnumArrayTrait;

    // Digital & Subscription Services
    case DIGITAL_STREAMING = 'digital_streaming';     // Netflix, Spotify
    case DIGITAL_DOWNLOADS = 'digital_downloads';     // Games, E-books
    case SOFTWARE_LICENSE = 'software_license';       // SaaS, Office 365

    // Goods (General)
    case RETAIL_GOODS = 'retail_goods';               // Physical products
    case GROCERIES = 'groceries';                     // Basic necessities (often VAT exempt)
    case PHARMACEUTICALS = 'pharmaceuticals';         // Medicine (often VAT exempt)
    case ALCOHOL = 'alcohol';                         // Excise + VAT
    case TOBACCO = 'tobacco';                         // Excise + VAT
    case FUEL = 'fuel';                               // Excise + VAT
    case AUTOMOBILES = 'automobiles';                 // Excise + VAT

    // Financial & Legal
    case LOAN = 'loan';                               // DST
    case INSURANCE = 'insurance';                     // DST
    case LEASE = 'lease';                             // DST for certain documents
    case BOND = 'bond';                               // DST

    // Services (General)
    case PROFESSIONAL_SERVICES = 'professional_services'; // Lawyers, consultants
    case CONSTRUCTION = 'construction';               // Contractors
    case TRANSPORTATION = 'transportation';           // Taxi, bus fares (VAT or exempt depending on type)
    case FOOD_SERVICE = 'food_service';               // Restaurants

    // Special
    case EXPORT = 'export';                           // VAT 0%
    case EDUCATION = 'education';                     // VAT exempt
    case HEALTHCARE = 'healthcare';                   // VAT exempt

    public function getLabel(): string
    {
        return match ($this) {
            // Digital & Subscription Services
            self::DIGITAL_STREAMING => 'Digital Streaming',       // Netflix, Spotify
            self::DIGITAL_DOWNLOADS => 'Digital Downloads',       // Games, E-books
            self::SOFTWARE_LICENSE => 'Software License',        // SaaS, Office 365

            // Goods (General)
            self::RETAIL_GOODS => 'Retail Goods',            // Physical products
            self::GROCERIES => 'Groceries',               // Basic necessities
            self::PHARMACEUTICALS => 'Pharmaceuticals',         // Medicine
            self::ALCOHOL => 'Alcohol',                 // Excise + VAT
            self::TOBACCO => 'Tobacco',                 // Excise + VAT
            self::FUEL => 'Fuel',                    // Excise + VAT
            self::AUTOMOBILES => 'Automobiles',

            // Financial & Legal
            self::LOAN => 'Loan',
            self::INSURANCE => 'Insurance',
            self::LEASE => 'Lease',
            self::BOND => 'Bond',

            // Services (General)
            self::PROFESSIONAL_SERVICES => 'Professional Services',
            self::CONSTRUCTION => 'Construction',
            self::TRANSPORTATION => 'Transportation',
            self::FOOD_SERVICE => 'Food Service',

            // Special
            self::EXPORT => 'Export',
            self::EDUCATION => 'Education',
            self::HEALTHCARE => 'Healthcare',
        };
    }

    public function toTaxClass(): TaxInterface
    {
        // for now
        return app(Vat::class);

        //        return match($this) {
        //            self::DIGITAL_STREAMING,self::DIGITAL_DOWNLOADS,self::SOFTWARE_LICENSE => app(Vat::class),
        //        };
    }
}
