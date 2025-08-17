<?php

declare(strict_types=1);

namespace App\Features\InviteUser\Notifications\UserInvitationMail;

use App\Features\InviteUser\Models\InviteUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class UserInvitationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected InviteUser $invite_user)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation to Join '.$this->invite_user->business->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'Features.InviteUser.Notifications.UserInvitationMail.user-invitation-mail',
            with: [
                'acceptUrl' => URL::signedRoute(
                    'register.user-invite',
                    [
                        'token' => $this->invite_user->code,
                    ],
                ),
                'business_name' => $this->invite_user->business->name,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
