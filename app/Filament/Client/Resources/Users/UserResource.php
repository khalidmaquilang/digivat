<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\Users;

use App\Features\User\Models\User;
use App\Filament\Client\Resources\Users\Pages\ListUsers;
use App\Filament\Client\Resources\Users\Schemas\UserForm;
use App\Filament\Client\Resources\Users\Tables\UsersTable;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Users;

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
        ];
    }
}
