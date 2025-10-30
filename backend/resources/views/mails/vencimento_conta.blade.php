@component('mail::message')
# {{ $greeting }} 👋

{{ $mensagem }}

Não esqueça de realizar o pagamento para evitar juros e multas!

Aqui estão os detalhes da conta:

@component('mail::panel')
**Descrição:** {{ $descricao }}<br>
**Valor:** R$ {{ $valor }}<br>
**Vencimento:** {{ $data_vencimento }}<br>
**Categoria:** {{ $categoria }}
@endcomponent

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Ver Lançamentos
@endcomponent

Obrigado,<br>
Equipe {{ config('app.name') }}
@endcomponent