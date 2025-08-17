<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\Users\Tables;

use App\Features\Business\Models\Business;
use App\Features\User\Actions\KickUserFromCompanyAction;
use App\Features\User\Models\User;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('Kick User')
                    ->color('danger')
                    ->icon(LucideIcon::UserMinus)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        /** @var ?Business $business */
                        $business = Filament::getTenant();
                        abort_if($business === null, 403);

                        app(KickUserFromCompanyAction::class)->handle($business, $record);

                        Notification::make()
                            ->success()
                            ->title('The user has been removed from the business.')
                            ->send();
                    })
                    ->hidden(function (User $record): bool {
                        /** @var ?Business $business */
                        $business = Filament::getTenant();
                        abort_if($business === null, 403);

                        /** @var ?User $auth_user */
                        $auth_user = auth()->user();
                        abort_if($auth_user === null, 403);

                        if ($business->owner->id !== $auth_user->id) {
                            return true;
                        }

                        return $record->id === $auth_user->id;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
