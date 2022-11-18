@extends('layouts.app')

@section('content')
    @if ($permitido)
        <div class="container">
            @include('assets.msg')
            <div class="row">
                <div class="float-end"><a href="/sistema" class="btn btn-primary btn-sm">Voltar</a></div>
                <h1>Estoque</h1>
            </div>

            <form class="needs-validation" novalidate method="post" action="{{ route('sistema.estoque') }}">
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
                <div class="modal fade" id="modalAddEstoque" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="formAddEstoque" method="post" action="{{ route('sistema.addEstoque') }}">
                                <div class="modal-header">
                                    <h3>Adicionar <span id="titulo"></span> no estoque</h3>
                                </div>
                                <div class="modal-body">
                                    @csrf
                                    @method('POST')
                                    <input class="d-none" id="item_id" name="item_id" type="text" />
                                    <h3>Quantidade atual <span id="atual"></span></h3>
                                    <h3>Quantidade para adicionar</h3>
                                    <input class="form-control" name="qtde" type="number" />
                                </div>
                            </form>
                            <div class="modal-footer">
                                <button class="btn btn-success" onclick="$('#formAddEstoque').submit()">Adicionar</button>
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalRemoveEstoque" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="formRemoveEstoque" method="post" action="{{ route('sistema.removeEstoque') }}">
                                <div class="modal-header">
                                    <h3>Remover <span id="tituloRemove"></span> do estoque</h3>
                                </div>
                                <div class="modal-body">
                                    @csrf
                                    @method('POST')
                                    <input class="d-none" id="item_idRemove" name="item_id" type="text" />
                                    <h3>Quantidade atual <span id="atualRemove"></span></h3>
                                    <h3>Quantidade para remover</h3>
                                    <input class="form-control" name="qtde" type="number" />
                                </div>
                            </form>
                            <div class="modal-footer">
                                <button class="btn btn-danger" onclick="$('#formRemoveEstoque').submit()">Remover</button>
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="container">
            <h1>Acesso negado para o usuário: {{ Auth::user()->name }}</h1>
        </div>
    @endif
@endsection
