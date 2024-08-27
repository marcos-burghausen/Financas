<x-mail::message>
# Introdução

Voçẽ acabou de adicionar uma nova despesa no valor de {{$expense->valor}}

<x-mail::button :url="''">
ok
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>