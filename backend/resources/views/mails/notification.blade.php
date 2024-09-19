<!DOCTYPE html>
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
</html>
