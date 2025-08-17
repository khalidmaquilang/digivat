<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Features\User\Actions\ChangePasswordAction;
use App\Features\User\Models\User;
use App\Filament\Components\Fields\TextInput\TextInput;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('Change password')
                        ->icon(LucideIcon::Lock)
                        ->schema([
                            TextInput::make('password')
                                ->password()
                                ->required(),
                        ])
                        ->action(function (User $record, array $data): void {
                            /** @var ?string $password */
                            $password = $data['password'] ?? null;
                            if ($password === null) {
                                return;
                            }

                            app(ChangePasswordAction::class)->handle($record, $password);

                            Notification::make()
                                ->title('Password changed successfully!')
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ])
                    ->hidden(function (User $record): bool {
                        /** @var ?string $user_id */
                        $user_id = auth()->id();
                        if ($user_id === null) {
                            return true;
                        }

                        return $user_id === $record->id;
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
