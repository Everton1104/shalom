@extends('layouts.app')

@section('content')
    @if ($permitido)
        <div class="container">
            @include('assets.msg')
            <div class="row">
                <div class="float-end"><a href="/sistema" class="btn btn-primary btn-sm">Voltar</a></div>
                <h1>Produtos</h1>
            </div>
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
            <div class="table-responsive">
                <table class="table table-hover table-striped mt-5">
                    <thead>
                        <th width="80"></th>
                        <th>NOME</th>
                        <th>ESTOQUE</th>
                        <th>CATEGORIA</th>
                        <th>VALOR COMPRA</th>
                        <th>VALOR VENDA</th>
                        <th>GRAMAS POR UNIDADE</th>
                    </thead>
                    <tbody>
                        @foreach ($itens as $item)
                            <tr>
                                <td>
                                    <a href="#" class="mx-1"
                                        onclick="editar(JSON.parse('{{ json_encode($item) }}'))"><i
                                            class="fa-solid fa-pen-to-square"></i></a>
                                    @if ($item->categoria != 5)
                                        <a href="#" class="mx-1"
                                            onclick="if(confirm('Deletar {!! $item->nome !!}?')){window.location.href='{!! route('sistema.deleteProduto', $item->id) !!}'}"
                                            style="color:red">
                                            <i class="fa-solid fa-trash"></i></a>
                                    @endif
                                <td>
                                    {{ $item->nome }}
                                </td>
                                <td>
                                    {{ $item->qtdeEstoque }}
                                </td>
                                <td>
                                    @php
                                        switch ($item->categoria) {
                                            case '1':
                                                echo 'Bebidas Alcoólicas';
                                                break;
                                            case '2':
                                                echo 'Porções';
                                                break;
                                            case '3':
                                                echo 'Bebidas';
                                                break;
                                            case '4':
                                                echo 'Doces e Sobremesas';
                                                break;
                                            case '5':
                                                echo 'Porções';
                                                break;
                                            case '6':
                                                echo 'Salgados';
                                                break;
                                        }
                                    @endphp
                                </td>
                                <td>
                                    R$ {{ number_format($item->valorCompra, 2, ',', '.') }}
                                </td>
                                <td>
                                    R$ {{ number_format($item->valor, 2, ',', '.') }}
                                </td>
                                @if ($item->categoria == 2 || $item->categoria == 5)
                                    <td>
                                        {{ $item->qtde }}g
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="table-responsive">
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
                        <select class="form-select" id="categoria" name="categoria" onchange="porcao(this)">
                            <option value="" selected disabled>Selecione uma opção</option>
                            <option value="1">Bebidas Alcoólicas</option>
                            <option value="2">Porções</option>
                            <option value="3">Bebidas</option>
                            <option value="4">Doces e Sobremesas</option>
                            <option value="6">Salgados</option>
                        </select>
                        <label id="labelValorAdd" for="valorAdd">Valor de Venda</label>
                        <input class="form-control" id="valorAdd" name="valor" type="number" />
                        <div id="meia" class="d-none">
                            <label for="qtdeInt">Quantidade em GRAMAS da porção inteira (Uma unidade)</label>
                            <input class="form-control" id="qtdeInt" name="qtdeInt" type="number" />
                            <hr>
                            <label for="valorMeiaAdd">Valor de Venda da Meia Porção</label>
                            <input class="form-control" id="valorMeiaAdd" name="valorMeia" type="number" />
                            <label for="qtdeMeia">Quantidade em GRAMAS da meia porção (Uma unidade)</label>
                            <input class="form-control" id="qtdeMeia" name="qtdeMeia" type="number" />
                            <hr>
                        </div>
                        <label id="valorCompraLabel" for="valorAdd">Valor de Compra</label>
                        <input class="form-control" id="valorCompraAdd" name="valorCompra" type="number" />
                        <label id="qtdeLabel" for="qtde">Quantidade no estoque</label>
                        <input class="form-control" id="qtde" name="qtde" type="number" />
                    </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="addProduto(event)">Enviar</button>
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
                        <input class="d-none" id="idEdt" name="id" type="text" />
                        <label id="nomeEdtLabel" for="nomeEdt">Nome</label>
                        <input class="form-control" id="nomeEdt" name="nome" type="text" />
                        <label id="categoriaEdtLabel" for="categoriaEdt">Categoria</label>
                        <select class="form-select" id="categoriaEdt" name="categoria" onchange="porcaoEdt(this)">
                            <option value="" selected disabled>Selecione uma opção</option>
                            <option value="1">Bebidas Alcoólicas</option>
                            <option value="2">Porções</option>
                            <option value="3">Bebidas</option>
                            <option value="4">Doces e Sobremesas</option>
                            <option value="5" hidden>meia</option>
                            <option value="6">Salgados</option>
                        </select>

                        <div id="intEdt" class="d-none">
                            <label for="qtdeIntEdt">Quantidade em GRAMAS da porção inteira (Uma unidade)</label>
                            <input class="form-control" id="qtdeIntEdt" name="qtdeInt" type="number" />
                        </div>

                        <div id="meiaEdt" class="d-none">
                            <label id="qtdeMeiaEdtLabel" for="qtdeMeiaEdt">Quantidade em GRAMAS de meia porção (Uma
                                unidade)</label>
                            <input class="form-control" id="qtdeMeiaEdt" name="qtdeMeia" type="number" />
                        </div>

                        <label id="labelValorEdt" for="valorEdt">Valor de Venda</label>
                        <input class="form-control" id="valorEdt" name="valor" type="number" />
                        <label id="valorCompraLabelEdt" for="valorEdt">Valor de Compra</label>
                        <input class="form-control" id="valorCompraEdt" name="valorCompra" type="number" />
                    </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="edtProduto(event)">Enviar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptEnd')
    <script>
        function porcao(opt) {
            if (opt.value == 2) {
                $('#meia').removeClass('d-none')
                $('#labelValorAdd').text('Valor de Venda da porção Inteira')
                $('#qtdeLabel').text('Quantidade no estoque em quilos (KG)')
                $('#valorCompraLabel').text('Valor de Compra por quilo (KG)')
            } else {
                $('#meia').addClass('d-none')
                $('#labelValorAdd').text('Valor de Venda')
                $('#qtdeLabel').text('Quantidade no estoque')
                $('#valorCompraLabel').text('Valor de Compra')
            }
        }

        function porcaoEdt(opt) { // OCULTAR OU MOSTRAR CAMPOS CONFORME CATEGORIA
            if (opt.value == 2) {
                $('#meiaEdt').removeClass('d-none')
                $('#labelValorEdt').text('Valor de Venda da porção Inteira')
                $('#qtdeLabelEdt').text('Quantidade no estoque em quilos (KG)')
                $('#valorCompraLabelEdt').text('Valor de Compra por quilo (KG)')
            } else {
                $('#meiaEdt').addClass('d-none')
                $('#labelValorEdt').text('Valor de Venda')
                $('#qtdeLabelEdt').text('Quantidade no estoque')
                $('#valorCompraLabelEdt').text('Valor de Compra')
            }
        }

        function editar(item) {
            console.log(item);
            if (item.categoria == 2) {

                $('#meiaEdt').addClass('d-none')
                $('#categoriaEdt').removeClass('d-none')
                $('#categoriaEdtLabel').removeClass('d-none')
                $('#nomeEdt').removeClass('d-none')
                $('#nomeEdtLabel').removeClass('d-none')
                $('#valorCompraLabelEdt').removeClass('d-none')
                $('#valorCompraEdt').removeClass('d-none')

                $('#intEdt').removeClass('d-none')
                $('#idEdt').val(item.id)
                $('#valorEdt').val(item.valor)
                $('#nomeEdt').val(item.nome.split(' -')[0])
                $('#categoriaEdt').val(item.categoria)
                $('#valorCompraEdt').val(item.valorCompra)
                $('#qtdeIntEdt').val(item.qtde)
                setTimeout(function() {
                    $('#modalEdt').modal('show')
                }, 150)
            } else if (item.categoria == 5) {

                $('#intEdt').addClass('d-none')

                $('#meiaEdt').removeClass('d-none')
                $('#categoriaEdt').addClass('d-none')
                $('#categoriaEdtLabel').addClass('d-none')
                $('#nomeEdt').addClass('d-none')
                $('#nomeEdtLabel').addClass('d-none')
                $('#valorCompraLabelEdt').addClass('d-none')
                $('#valorCompraEdt').addClass('d-none')
                $('#qtdeMeiaEdtLabel').text('Quantidade em GRAMAS de MEIA porção de ' + item.nome.split(' -')[0] +
                    ' (Uma unidade)')
                $('#idEdt').val(item.id)
                $('#valorEdt').val(item.valor)
                $('#nomeEdt').val(item.nome)
                $('#valorCompraEdt').val(item.valorCompra)
                $('#categoriaEdt').val(item.categoria)
                $('#qtdeMeiaEdt').val(item.qtde)
                setTimeout(function() {
                    $('#modalEdt').modal('show')
                }, 150)
            } else {
                $('#idEdt').val(item.id)
                $('#nomeEdt').val(item.nome.split(' -')[0])
                $('#categoriaEdt').val(item.categoria)
                $('#valorEdt').val(item.valor)
                $('#valorCompraEdt').val(item.valorCompra)
                setTimeout(function() {
                    $('#modalEdt').modal('show')
                }, 150)
            }
        }

        function addProduto(e) {
            e.preventDefault();
            if (parseFloat($('#valorCompraAdd').val()) >= parseFloat($('#valorAdd').val())) {
                alert("ATENÇÃO VOCÊ ESTA CADASTRANDO UM PRODUTO COM VALOR DE VENDA MENOR OU IGUAL AO PREÇO DE COMPRA!");
            } else {
                $('#formAdd').submit()
            }
        }

        function edtProduto(e) {
            e.preventDefault();
            if (parseFloat($('#valorCompraEdt').val()) >= parseFloat($('#valorEdt').val())) {
                alert("ATENÇÃO VOCÊ ESTA CADASTRANDO UM PRODUTO COM VALOR DE VENDA MENOR OU IGUAL AO PREÇO DE COMPRA!");
            } else {
                $('#formEdt').submit()
            }
        }
    </script>
@endsection
