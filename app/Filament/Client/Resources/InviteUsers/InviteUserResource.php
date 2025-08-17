<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\InviteUsers;

use App\Features\InviteUser\Models\InviteUser;
use App\Filament\Client\Resources\InviteUsers\Pages\ListInviteUsers;
use App\Filament\Client\Resources\InviteUsers\Schemas\InviteUserForm;
use App\Filament\Client\Resources\InviteUsers\Tables\InviteUsersTable;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class InviteUserResource extends Resource
{
    protected static ?string $model = InviteUser::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::UserPlus;

    public static function form(Schema $schema): Schema
    {
        return InviteUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InviteUsersTable::configure($table);
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
            'index' => ListInviteUsers::route('/'),
        ];
    }
}
