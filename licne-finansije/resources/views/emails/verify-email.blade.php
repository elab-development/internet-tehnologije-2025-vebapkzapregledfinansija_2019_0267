<x-mail::message>
# Zdravo, {{ $user->ime }}!

Hvala Vam što ste se registrovali na našu platformu. Molimo Vas da potvrdite Vašu email adresu klikom na dugme ispod:

<x-mail::button :url="$verificationUrl">
Verifikuj Email Adresu
</x-mail::button>

Hvala,<br>
{{ config('app.name') }}
</x-mail::message>
