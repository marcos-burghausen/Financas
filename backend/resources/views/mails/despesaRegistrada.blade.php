<x-mail::message>
# Introdução

Uma novoçẽ acabou de adicionar uma nova despesa no valor de {{ 'R$ ' . number_format($expense->valor / 100, 2, ',', '.') }}

<x-mail::button :url="''">
ok
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>