<?php

declare(strict_types=1);

namespace App\Features\InviteUser\Filament\Pages;

use App\Features\InviteUser\Actions\GetInvitationByCode;
use App\Features\InviteUser\Models\InviteUser;
use App\Features\User\Actions\GetUserByEmail;
use App\Features\User\Models\User;
use App\Filament\Client\Pages\Auth\Register;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

class RegisterInvited extends Register
{
    #[Url]
    public string $token = '';

    public ?InviteUser $invite_user = null;

    protected GetInvitationByCode $get_invitation_by_code;

    protected GetUserByEmail $get_user_by_email;

    public function boot(
        GetInvitationByCode $get_invitation_by_code,
        GetUserByEmail $get_user_by_email
    ): void {
        $this->get_invitation_by_code = $get_invitation_by_code;
        $this->get_user_by_email = $get_user_by_email;
    }

    public function afterFill(): void
    {
        $this->invite_user = $this->get_invitation_by_code->handle($this->token);
        abort_if(! $this->invite_user instanceof \App\Features\InviteUser\Models\InviteUser, 404);

        /** @var ?User $user */
        $user = $this->get_user_by_email->handle($this->invite_user->email);
        if ($user === null) {
            $this->form->fill([
                'email' => $this->invite->email,
            ]);

            return;
        }

        $this->connectToBusiness($user);

        $this->redirect('/');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        /** @var User $user */
        $user = $this->getUserModel()::create($data);

        $this->connectToBusiness($user);

        return $user;
    }

    protected function connectToBusiness(User $user): void
    {
        $business = $this->invite_user->business;

        $business->members()->attach($user);

        Filament::auth()->login($user);

        $this->invite_user->delete();
    }
}
