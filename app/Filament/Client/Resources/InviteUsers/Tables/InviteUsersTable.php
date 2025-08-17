<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\InviteUsers\Tables;

use App\Features\InviteUser\Notifications\UserInvitationMail\UserInvitationMail;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class InviteUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('resend')
                    ->label('Resend')
                    ->icon(LucideIcon::Send)
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        Mail::to($record->email)->send(new UserInvitationMail($record));

                        Notification::make()
                            ->success()
                            ->title('Invitation sent')
                            ->body('Invitation has been successfully sent to the recipient.')
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
