<?php

declare(strict_types=1);

namespace Features\Shared\Enums;

enum CategoryTypeEnum: string
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
}
