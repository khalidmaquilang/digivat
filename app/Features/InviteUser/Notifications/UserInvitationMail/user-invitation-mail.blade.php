<x-mail::message>
Hello,

You have been invited to join {{ $business_name }}

To accept the invitation, click on the button below and create an account.

<x-mail::button :url="$acceptUrl">
    Join Business
</x-mail::button>

If you did not expect to receive an invitation to this business, you may disregard this email.

Thanks,
{{ config('app.name') }}
</x-mail::message>