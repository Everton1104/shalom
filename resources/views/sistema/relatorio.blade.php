@extends('layouts.app')

@section('content')
    @php
        $totalDebito = 0;
        $totalCredito = 0;
        $totalPix = 0;
        $totalDinheiro = 0;
        $totalGeral = 0;
        $totalPerda = 0;
    @endphp
    @if ($permitido)
        <div class="container">
            @include('assets.msg')
            <div class="row">
                <div class="float-end"><a href="/sistema" class="btn btn-primary btn-sm">Voltar</a></div>
                <h1>Relatórios</h1>
            </div>

            <form class="needs-validation" novalidate method="post" action="{{ route('sistema.relatorio') }}">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label for="dataInit" class="form-label">Selecione a data de início</label>
                        <input class="form-control" type="date" id="dataInit" name="dataInit" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label for="dataFim" class="form-label">Selecione a data de término</label>
                        <input class="form-control" type="date" id="dataFim" name="dataFim" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary my-3">Buscar</button>
            </form>

            @if (isset($resumo))
                <h2>Resumo do dia {{ date('d/m/Y', strtotime($request->dataInit)) }} ao dia
                    {{ date('d/m/Y', strtotime($request->dataFim)) }}</h2>
                <div class="table-responsive">
                    <table class="table table-hover table-striped mt-5">
                        <thead>
                            <th>Dia</th>
                            <th>Nome</th>
                            <th>Tipo de pagamento</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Valor</th>
                            <th>Total</th>
                        </thead>
                        <tbody>
                            @foreach ($resumo as $item)
                                @if (!$item->itemNome)
                                    <td>
                                        {{ date('d/m/Y', strtotime($item->updated_at)) }} as
                                        {{ date('H:i:s', strtotime($item->updated_at)) }}
                                    </td>
                                    <td>
                                        {{ $item->nome }}
                                    </td>
                                    <td>
                                        @php
                                            switch ($item->tipo) {
                                                case '1':
                                                    echo 'Débito';
                                                    break;
                                                case '2':
                                                    echo 'Crédito';
                                                    break;
                                                case '3':
                                                    echo 'PIX';
                                                    break;
                                                case '4':
                                                    echo 'Dinheiro';
                                                    break;
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        {{ $item->obs }}
                                    </td>
                                    <td>
                                        {{ $item->qtde }}
                                    </td>
                                    <td>
                                        -
                                    </td>
                                    <td>
                                        -
                                    </td>
                                @else
                                    <tr>
                                        <td>
                                            {{ date('d/m/Y', strtotime($item->updated_at)) }} as
                                            {{ date('H:i:s', strtotime($item->updated_at)) }}
                                        </td>
                                        <td>
                                            {{ $item->nome }}
                                        </td>
                                        <td>
                                            @php
                                                switch ($item->tipo) {
                                                    case '1':
                                                        echo 'Débito';
                                                        break;
                                                    case '2':
                                                        echo 'Crédito';
                                                        break;
                                                    case '3':
                                                        echo 'PIX';
                                                        break;
                                                    case '4':
                                                        echo 'Dinheiro';
                                                        break;
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            {{ $item->itemNome }}
                                        </td>
                                        <td>
                                            {{ $item->qtde }}
                                        </td>
                                        <td>
                                            R$ {{ number_format($item->valor, 2, ',', '.') }}
                                        </td>
                                        <td>
                                            R$ {{ number_format($item->valor * $item->qtde, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @php
                                        switch ($item->tipo) {
                                            case '1':
                                                $totalDebito += $item->valor * $item->qtde;
                                                break;
                                            case '2':
                                                $totalCredito += $item->valor * $item->qtde;
                                                break;
                                            case '3':
                                                $totalPix += $item->valor * $item->qtde;
                                                break;
                                            case '4':
                                                $totalDinheiro += $item->valor * $item->qtde;
                                                break;
                                        }
                                        $totalGeral += $item->valor * $item->qtde;
                                    @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card my-3">
                    <div class="card-body row">
                        <div class="col-6">
                            <div class="my-2">
                                Total cartão de DÉBITO: R$ {{ number_format($totalDebito, 2, ',', '.') }}
                            </div>
                            <div class="my-2">
                                Total cartão de CRÉDITO: R$ {{ number_format($totalCredito, 2, ',', '.') }}
                            </div>
                            <div class="my-2">
                                Total PIX: R$ {{ number_format($totalPix, 2, ',', '.') }}
                            </div>
                            <div class="my-2">
                                Total DINHEIRO: R$ {{ number_format($totalDinheiro, 2, ',', '.') }}
                            </div>
                            <div class="my-2">
                                Total de pagamentos: <span style="color:green"> + R$
                                    {{ number_format($totalGeral, 2, ',', '.') }}</span>
                            </div>
                            <div class="my-5">
                                <h4>
                                    Total de pagamentos menos as perdas:
                                    <span id="totalgeral"></span>
                                </h4>
                            </div>
                        </div>
                        <div class="col-6" style="border-left: 1px solid;">
                            @if (isset($extravio) && isset($bonificacao))
                                <h4>Extravio</h4>
                                @foreach ($extravio as $item)
                                    {{ $item->obs }}<br>
                                    {{ $item->qtde }} X {{ $item->itemNome }} a R$
                                    {{ number_format($item->valor, 2, ',', '.') }} cada.<br>
                                    Total: R$ {{ number_format($item->valor * $item->qtde, 2, ',', '.') }}
                                    <hr>
                                    <?php $totalPerda += $item->valor * $item->qtde; ?>
                                @endforeach
                                <h4>Bonificações</h4>
                                @foreach ($bonificacao as $item)
                                    {{ $item->obs }}<br>
                                    Para {{ $item->nome }}<br>
                                    {{ $item->qtde }} X {{ $item->itemNome }} a R$
                                    {{ number_format($item->valor, 2, ',', '.') }} cada.<br>
                                    Total: R$ {{ number_format($item->valor * $item->qtde, 2, ',', '.') }}
                                    <hr>
                                    <?php $totalPerda += $item->valor * $item->qtde; ?>
                                @endforeach
                                Total perdas: <span style="color:red">- R$
                                    {{ number_format($totalPerda, 2, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <script>
            (() => {
                'use strict'
                const forms = document.querySelectorAll('.needs-validation')
                Array.from(forms).forEach(form => {
                    form.addEventListener('submit', event => {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
            })()

            $('#totalgeral').text('R$ {{ number_format($totalGeral - $totalPerda, 2, ',', '.') }}')
        </script>
    @else
        <div class="container">
            <h1>Acesso negado para o usuário: {{ Auth::user()->name }}</h1>
        </div>
    @endif
@endsection
