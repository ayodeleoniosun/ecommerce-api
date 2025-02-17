<x-mail::message>
    <p>Hi {{ ucfirst($verification->user->firstname) }},</p>
    <p>You requested for verification mail to be resent. </p>
    <p> Click <a href="{{ $verification_link }}" style="font-weight: bold"> HERE </a> to verify your email address. </p>
</x-mail::message>

