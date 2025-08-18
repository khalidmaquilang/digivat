<?php

declare(strict_types=1);

namespace App\Filament\Resources\CreativeDomains;

use App\Features\CreativeDomain\Models\CreativeDomain;
use App\Filament\Resources\CreativeDomains\Pages\CreateCreativeDomain;
use App\Filament\Resources\CreativeDomains\Pages\EditCreativeDomain;
use App\Filament\Resources\CreativeDomains\Pages\ListCreativeDomains;
use App\Filament\Resources\CreativeDomains\Schemas\CreativeDomainForm;
use App\Filament\Resources\CreativeDomains\Tables\CreativeDomainsTable;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreativeDomainResource extends Resource
{
    protected static ?string $model = CreativeDomain::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Atom;

    public static function form(Schema $schema): Schema
    {
        return CreativeDomainForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreativeDomainsTable::configure($table);
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
            'index' => ListCreativeDomains::route('/'),
            'create' => CreateCreativeDomain::route('/create'),
            'edit' => EditCreativeDomain::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<CreativeDomain>
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
