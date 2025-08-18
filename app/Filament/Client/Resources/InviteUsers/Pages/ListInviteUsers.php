<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\InviteUsers\Pages;

use App\Features\Business\Models\Business;
use App\Features\InviteUser\Models\InviteUser;
use App\Features\InviteUser\Notifications\UserInvitationMail\UserInvitationMail;
use App\Features\User\Models\User;
use App\Filament\Client\Resources\InviteUsers\InviteUserResource;
use Exception;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;

class ListInviteUsers extends ListRecords
{
    protected static string $resource = InviteUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Invite User')
                ->createAnother(false)
                ->mutateDataUsing(function (array $data) {
                    /** @var ?string $email */
                    $email = $data['email'] ?? null;
                    if ($email === null) {
                        throw new Exception('No email provided.');
                    }

                    $data['code'] = substr(md5(random_int(0, 9).$email.time()), 0, 32);

                    return $data;
                })
                ->before(function (CreateAction $action, array $data): void {
                    /** @var ?Business $business */
                    $business = Filament::getTenant();
                    abort_if($business === null, 403);

                    /** @var ?User $user */
                    $user = $business->members()->where('email', $data['email'])->first();
                    if ($user === null) {
                        return;
                    }

                    Notification::make()
                        ->danger()
                        ->title('User already exist')
                        ->body('This user is already a member of this business.')
                        ->send();

                    $action->halt();
                })
                ->after(function (InviteUser $record): void {
                    Mail::to($record->email)->send(new UserInvitationMail($record));
                })
                ->successNotificationTitle('Invitation sent successfully!'),
        ];
    }
}
