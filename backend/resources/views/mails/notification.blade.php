{{-- <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificação de Ação</title>
</head>
<body>
    <h1>Olá, {{ $user->name }}!</h1>
    <p>Estamos notificando que uma ação foi realizada em seu sistema.</p>

    <p><strong>Ação:</strong> {{ $action }}</p>
    <p><strong>Tipo:</strong> {{ $itemType }}</p>
    <p><strong>Descrição do Item:</strong> {{ $itemName }}</p>

    <p>Se você não realizou esta ação, entre em contato com o suporte imediatamente.</p>

    <p>Atenciosamente,<br>
    Sua equipe de suporte</p>
</body>
</html> --}}


{{-- @component('mail::button', ['url' => config('app.url')])
Abrir o sistema
@endcompone --}}



@component('mail::message')
# Notificação de Ação

Olá, **{{ $user->name }}**!

Uma ação foi realizada no sistema.

@component('mail::panel')
**Ação:** {{ $action }}  
**Tipo:** {{ $itemType }}  
**Item:** {{ $itemName }}
@endcomponent

Se você não reconhece esta ação, entre em contato com o suporte imediatamente.

@slot('subcopy')
Se o botão não funcionar, copie e cole o link no navegador: {{ config('app.url') }}
@endslot

Obrigado,<br>
{{ config('app.name') }}
@endcomponent

