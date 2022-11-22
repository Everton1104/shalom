@extends('layouts.app')

@section('content')
    @if ($permitido)
        <div class="container">
            @include('assets.msg')
            <div class="row">
                <div class="float-end"><a href="/sistema" class="btn btn-primary btn-sm">Voltar</a></div>
                <h1>Histórico de operações</h1>
            </div>

            {{-- FAZER BUSCA NO HISTORICO POR TIPO DE OPERACAO E USUARIO --}}

            <form class="needs-validation" novalidate method="post" action="{{ route('sistema.historico') }}">
                @csrf
                @method('POST')
                <div class="input-group my-3">
                    <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <input class="form-control" type="text" id="buscar" name="buscar">
                </div>
            </form>

            @if (isset($itens))
                <div class="table-responsive">
                    <table class="table table-hover table-striped mt-5">
                        <thead>
                            <th width="80"></th>
                            <th>NOME</th>
                            <th>QTDE EM ESTOQUE</th>
                            <th>VALOR DE COMPRA</th>
                            <th>VALOR DE VENDA</th>
                        </thead>
                        <tbody>
                            @foreach ($itens as $item)
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-2">
                                                <a href="#"
                                                    onclick="
                                                    $('#atual').text({{ $item->qtde }})
                                                    $('#titulo').text('{{ $item->nome }}')
                                                    $('#item_id').val('{{ $item->item_id }}')
                                                    $('#modalAddEstoque').modal('show')
                                                ">
                                                    <i style="color:green" class="fas fa-plus-circle"></i>
                                                </a>
                                            </div>
                                            <div class="col-2">
                                                <a href="#"
                                                    onclick="
                                                    $('#atualRemove').text({{ $item->qtde }})
                                                    $('#tituloRemove').text('{{ $item->nome }}')
                                                    $('#item_idRemove').val('{{ $item->item_id }}')
                                                    $('#modalRemoveEstoque').modal('show')
                                                ">
                                                    <i style="color:red" class="fas fa-minus-circle"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $item->nome }}
                                    </td>
                                    <td>
                                        @if ($item->qtde < 0)
                                            <span style="color:red">Atenção estoque NEGATIVO de {{ $item->qtde }}.
                                            @else
                                                {{ $item->qtde }}
                                        @endif
                                    </td>
                                    <td>
                                        R$ {{ number_format($item->valor, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        R$ {{ number_format($item->valorVenda, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="container">
            <h1>Acesso negado para o usuário: {{ Auth::user()->name }}</h1>
        </div>
    @endif
@endsection
