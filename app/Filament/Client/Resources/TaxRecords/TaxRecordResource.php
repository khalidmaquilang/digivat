<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords;

use App\Features\TaxRecord\Models\TaxRecord;
use App\Filament\Client\Resources\TaxRecords\Pages\CreateTaxRecord;
use App\Filament\Client\Resources\TaxRecords\Pages\EditTaxRecord;
use App\Filament\Client\Resources\TaxRecords\Pages\ListTaxRecords;
use App\Filament\Client\Resources\TaxRecords\Pages\ViewTaxRecord;
use App\Filament\Client\Resources\TaxRecords\Schemas\TaxRecordForm;
use App\Filament\Client\Resources\TaxRecords\Schemas\TaxRecordInfolist;
use App\Filament\Client\Resources\TaxRecords\Tables\TaxRecordsTable;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TaxRecordResource extends Resource
{
    protected static ?string $model = TaxRecord::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Receipt;

    public static function form(Schema $schema): Schema
    {
        return TaxRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaxRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxRecords::route('/'),
            'create' => CreateTaxRecord::route('/create'),
            'view' => ViewTaxRecord::route('/{record}'),
            'edit' => EditTaxRecord::route('/{record}/edit'),
        ];
    }
}
