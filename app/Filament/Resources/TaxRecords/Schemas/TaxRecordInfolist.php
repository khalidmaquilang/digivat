<?php

declare(strict_types=1);

namespace App\Filament\Resources\TaxRecords\Schemas;

use App\Features\TaxRecord\Models\TaxRecord;
use Filament\Schemas\Schema;

class TaxRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(TaxRecord::getInfolistSchema());
    }
}
