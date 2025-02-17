<x-mail::message>
    <p>Hi {{ ucfirst($user->firstname) }},</p>
    <p>Your registration to {{ config('app.name') }} was successful. </p>
    <p>However, you would not be able to access the platform until verify your email address. </p>
    <p> Click <a href="{{ $verification_link }}" style="font-weight: bold"> HERE </a> to verify your email address. </p>
</x-mail::message>

