@component('mail::message')
# Olá! 👋

Este é um lembrete amigável do **{{ config('app.name') }}**.

Você possui **{{ $totalLancamentos }}** contas que irão vencer nos próximos **{{ $dias }} dias**.

O valor total desses lançamentos é de **R$ {{ $valorTotal }}**.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Ver meus Lançamentos
@endcomponent

Não se esqueça de realizar os pagamentos ou recebimentos para manter suas finanças em dia.

Obrigado,<br>
Equipe {{ config('app.name') }}
@endcomponent