<?php

declare(strict_types=1);

namespace App\Filament\Resources\TaxRecords\Schemas;

use Filament\Schemas\Schema;

class TaxRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
