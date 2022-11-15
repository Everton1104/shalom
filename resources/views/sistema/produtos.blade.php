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
                    <th width="100">VALOR</th>
                </thead>
                <tbody>
                    @foreach ($itens as $item)
                        <tr>
                            <td>
                                <a href="#" class="mx-1"
                                    onclick="editar('{!! $item->id !!}', '{!! $item->nome !!}', '{!! $item->valor !!}')"><i
                                        class="fa-solid fa-pen-to-square"></i></a>
                                <a href="#" class="mx-1"
                                    onclick="if(confirm('Deletar {!! $item->nome !!}?')){window.location.href='{!! route('sistema.deleteProduto', $item->id) !!}'}"
                                    style="color:red">
                                    <i class="fa-solid fa-trash"></i></a>
                            <td>
                                {{ $item->nome }}
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
                        <label for="nome">Valor</label>
                        <input class="form-control" id="valorAdd" name="valor" type="number" />
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
                        <label for="nome">Nome</label>
                        <input class="form-control" id="nomeEdt" name="nome" type="text" />
                        <label for="nome">Valor</label>
                        <input class="form-control" id="valorEdt" name="valor" type="number" />
                        <input class="d-none" id="id" name="id" type="number" />
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
        function editar(id, nome, valor) {
            $('#id').val(id)
            $('#nomeEdt').val(nome)
            $('#valorEdt').val(valor)
            setTimeout(function() {
                $('#modalEdt').modal('show')
            }, 150)
        }


        window.addEventListener('keydown', (e) => {
            $('#code').focus();
        })

        $.typeahead({
            input: '.js-typeahead',
            minLength: 3,
            maxItem: 10,
            order: "asc",
            display: "nome",
            source: {
                ajax: {
                    url: "{{ route('searchItem') }}",
                    data: {
                        search: $('#procItem').val()
                    }
                }
            },
            callback: {
                onClickAfter: function(node, a, item, event) {
                    event.preventDefault();
                    qtde = prompt("Digite a QUANTIDADE:")
                    $('#itemId').val(item.id)
                    $('#qtde').val(qtde)
                    $('#modalAddForm').submit()
                }
            }
        });
    </script>
@endsection
