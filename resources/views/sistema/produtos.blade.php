@extends('layouts.app')

@section('content')
    @if ($permitido)
        <div class="container">
            @include('assets.msg')
            <h1>Produtos</h1>
            <button type="button" class="btn btn-primary mt-3" onclick="$('#modalAdd').modal('show')">Adicionar
                Produto</button>

            <form method="post" action="{{ route('sistema.searchProduto') }}">
                @csrf
                @method('POST')
                <div class="input-group my-3">
                    <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <input class="form-control" type="text" id="search" name="search">
                </div>
            </form>

            <table class="table table-hover table-striped mt-5">
                <thead>
                    <th width="80"></th>
                    <th>NOME</th>
                    <th width="200">CATEGORIA</th>
                    <th width="100">VALOR COMPRA</th>
                    <th width="100">VALOR VENDA</th>
                </thead>
                <tbody>
                    @foreach ($itens as $item)
                        <tr>
                            <td>
                                <a href="#" class="mx-1"
                                    onclick="editar('{!! $item->id !!}', '{!! $item->nome !!}', '{!! $item->valor !!}', '{!! $item->valorCompra !!}', '{!! $item->categoria !!}')"><i
                                        class="fa-solid fa-pen-to-square"></i></a>
                                <a href="#" class="mx-1"
                                    onclick="if(confirm('Deletar {!! $item->nome !!}?')){window.location.href='{!! route('sistema.deleteProduto', $item->id) !!}'}"
                                    style="color:red">
                                    <i class="fa-solid fa-trash"></i></a>
                            <td>
                                {{ $item->nome }}
                            </td>
                            <td>
                                {{ $item->categoria }}
                            </td>
                            <td>
                                R$ {{ number_format($item->valorCompra, 2, ',', '.') }}
                            </td>
                            <td>
                                R$ {{ number_format($item->valor, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="container">
            <h1>Acesso negado para o usuário: {{ Auth::user()->name }}</h1>
        </div>
    @endif
    <div class="modal fade" id="modalAdd" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formAdd" method="post" action="{{ route('sistema.novoProduto') }}">
                    <div class="modal-header">
                        <h3>Cadastrar Novo Produto</h3>
                    </div>
                    <div class="modal-body">
                        @csrf
                        @method('POST')
                        <label for="nome">Nome</label>
                        <input class="form-control" id="nomeAdd" name="nome" type="text" />
                        <label for="categoria">Categoria</label>
                        <select class="form-control" id="categoria" name="categoria">
                            <option value="" selected disabled>Selecione uma opção</option>
                            <option value="1">Bebidas Alcoólicas</option>
                            <option value="2">Porções</option>
                            <option value="3">Bebidas</option>
                            <option value="4">Doces e Sobremesas</option>
                        </select>
                        <label for="valorAdd">Valor de Venda</label>
                        <input class="form-control" id="valorAdd" name="valor" type="number" />
                        <label for="valorAdd">Valor de Compra</label>
                        <input class="form-control" id="valorCompraAdd" name="valorCompra" type="number" />
                        <label for="qtde">Quantidade no estoque</label>
                        <input class="form-control" id="qtde" name="qtde" type="number" />
                    </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="$('#formAdd').submit()">Enviar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEdt" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEdt" method="post" action="{{ route('sistema.editarProduto') }}">
                    <div class="modal-header">
                        <h3>Editar Produto</h3>
                    </div>
                    <div class="modal-body">
                        @csrf
                        @method('POST')
                        <input class="d-none" id="id" name="id" type="text" />
                        <label for="nome">Nome</label>
                        <input class="form-control" id="nomeEdt" name="nome" type="text" />
                        <label for="categoria">Categoria</label>
                        <select class="form-control" id="categoriaEdt" name="categoria">
                            <option value="" selected disabled>Selecione uma opção</option>
                            <option value="1">Bebidas Alcoólicas</option>
                            <option value="2">Porções</option>
                            <option value="3">Bebidas</option>
                            <option value="4">Doces e Sobremesas</option>
                        </select>
                        <label for="valorEdt">Valor de Venda</label>
                        <input class="form-control" id="valorEdt" name="valor" type="number" />
                        <label for="valorCompraEdt">Valor de Compra</label>
                        <input class="form-control" id="valorCompraEdt" name="valorCompra" type="number" />
                    </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="$('#formEdt').submit()">Enviar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptEnd')
    <script>
        function editar(id, nome, valor, valorCompra, categoria) {
            $('#id').val(id)
            $('#nomeEdt').val(nome)
            $('#categoriaEdt').val(categoria)
            $('#valorEdt').val(valor)
            $('#valorCompraEdt').val(valorCompra)
            setTimeout(function() {
                $('#modalEdt').modal('show')
            }, 150)
        }
    </script>
@endsection
