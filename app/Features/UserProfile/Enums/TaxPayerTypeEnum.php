<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;
use Filament\Support\Contracts\HasLabel;

enum TaxPayerTypeEnum: string implements HasLabel
{
    use EnumArrayTrait;

    case SingleProprietorshipResidentCitizen = 'single_proprietorship_only_resident_citizen';
    case MixedCompensationAndProfessional = 'mixed_income_earner_compensation_income_earner_and_professional';
    case ResidentAlienSingleProprietorship = 'resident_alien_single_proprietorship';
    case MixedCompensationSingleProprietorshipAndProfessional = 'mixed_income_earner_compensation_income_earner_single_proprietorship_and_professional';
    case ResidentAlienProfessional = 'resident_alien_professional';
    case NonResidentAlienEngagedInTrade = 'non_resident_alien_engaged_in_trade_or_business';
    case ProfessionalLicensed = 'professional_licensed_prc_ibp';
    case EstateFilipinoCitizen = 'estate_filipino_citizen';
    case ProfessionalInGeneral = 'professional_in_general';
    case EstateForeignNational = 'estate_foreign_national';
    case ProfessionalAndSingleProprietor = 'professional_and_single_proprietor';
    case TrustFilipinoCitizen = 'trust_filipino_citizen';
    case MixedCompensationAndSingleProprietor = 'mixed_income_earner_compensation_income_earner_and_single_proprietor';
    case TrustForeignNational = 'trust_foreign_national';

    public function getLabel(): string
    {
        return match ($this) {
            self::SingleProprietorshipResidentCitizen => 'Single Proprietorship Only (Resident Citizen)',
            self::MixedCompensationAndProfessional => 'Mixed Income Earner – Compensation Income Earner & Professional',
            self::ResidentAlienSingleProprietorship => 'Resident Alien – Single Proprietorship',
            self::MixedCompensationSingleProprietorshipAndProfessional => 'Mixed Income Earner – Compensation Income Earner, Single Proprietorship & Professional',
            self::ResidentAlienProfessional => 'Resident Alien - Professional',
            self::NonResidentAlienEngagedInTrade => 'Non-Resident Alien Engaged in Trade/Business',
            self::ProfessionalLicensed => 'Professional – Licensed (PRC, IBP)',
            self::EstateFilipinoCitizen => 'Estate – Filipino Citizen',
            self::ProfessionalInGeneral => 'Professional – In General',
            self::EstateForeignNational => 'Estate – Foreign National',
            self::ProfessionalAndSingleProprietor => 'Professional and Single Proprietor',
            self::TrustFilipinoCitizen => 'Trust – Filipino Citizen',
            self::MixedCompensationAndSingleProprietor => 'Mixed Income Earner – Compensation Income Earner & Single Proprietor',
            self::TrustForeignNational => 'Trust – Foreign National',
        };
    }
}
