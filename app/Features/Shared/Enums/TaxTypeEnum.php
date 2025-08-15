<?php

declare(strict_types=1);

namespace App\Features\Shared\Enums;

enum TaxTypeEnum: string
{
    use EnumArrayTrait;

    case VAT_12 = 'vat_12';                    // 12% Value-Added Tax
    case VAT_0 = 'vat_0';                      // 0% VAT (Zero-rated)
    case VAT_EXEMPT = 'vat_exempt';            // VAT-Exempt Transactions
    case PERCENTAGE_TAX_3 = 'percentage_tax_3'; // 3% Percentage Tax (non-VAT)
    case DST = 'dst';                          // Documentary Stamp Tax
    case EXCISE_TAX = 'excise_tax';            // Excise Tax (goods like alcohol, tobacco, fuel)
    case WITHHOLDING_EXPANDED = 'withholding_expanded'; // EWT
    case WITHHOLDING_FINAL = 'withholding_final';       // Final Withholding Tax
    case INCOME_TAX = 'income_tax';            // For direct income reporting
}
