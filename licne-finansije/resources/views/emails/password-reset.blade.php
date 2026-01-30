<x-mail::message>
# Zdravo {{ $user->name }},

Primili smo zahtev za resetovanje Vaše lozinke. Kliknite na dugme ispod da biste postavili novu lozinku:

<x-mail::button :url="$resetUrl">
Resetuj lozinku
</x-mail::button>

Vaš token za resetovanje lozinke je: 
{{ $token }}

Ako niste tražili resetovanje lozinke, molimo Vas da ignorišete ovaj email.

Hvala,<br>
{{ config('app.name') }}
</x-mail::message>
