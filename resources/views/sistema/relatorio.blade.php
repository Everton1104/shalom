@extends('layouts.app')

@section('content')
    @if ($permitido)
        <div class="container">
            @include('assets.msg')
            <h1>Relatórios</h1>

            <form class="needs-validation" novalidate method="post" action="{{ route('sistema.relatorio') }}">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-2">
                        <label for="dataInit" class="form-label">Selecione a data de início</label>
                        <input class="form-control" type="date" id="dataInit" name="dataInit" required>
                    </div>
                    <div class="col-2">
                        <label for="dataFim" class="form-label">Selecione a data de término</label>
                        <input class="form-control" type="date" id="dataFim" name="dataFim" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary my-3">Buscar</button>
            </form>

            @if (isset($resumo))
                <h2>Resumo do dia {{ date('d/m/Y', strtotime($request->dataInit)) }} ao dia
                    {{ date('d/m/Y', strtotime($request->dataFim)) }}</h2>
                {{-- {"id":1,"item_id":2,"card_id":1,"qtde":5,"pago":1,"tipo":1,"nome":"Everton","obs":null,"created_at":"2022-11-16T02:43:41.000000Z","updated_at":"2022-11-12T02:43:48.000000Z"} --}}
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
                        @php
                            $totalDebito = 0;
                            $totalCredito = 0;
                            $totalPix = 0;
                            $totalDinheiro = 0;
                            $totalGeral = 0;
                        @endphp
                        @foreach ($resumo as $item)
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
                        @endforeach
                    </tbody>
                </table>
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
                    Total de pagamentos: R$ {{ number_format($totalGeral, 2, ',', '.') }}
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
        </script>
    @else
        <div class="container">
            <h1>Acesso negado para o usuário: {{ Auth::user()->name }}</h1>
        </div>
    @endif
@endsection
