@extends('layouts.app')

@section('content')
    @if ($permitido)
        <div class="container">
            @include('assets.msg')
            <h1>Procurar por Nome</h1>

            <form method="post" action="{{ route('sistema.searchNomeAberto') }}">
                @csrf
                @method('POST')
                <div class="input-group my-3">
                    <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <input class="form-control" type="text" id="search" name="nome">
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover table-striped mt-5">
                    <thead>
                        <th width="40"></th>
                        <th>NOME</th>
                        <th>Data de Entrada</th>
                    </thead>
                    <tbody>
                        @foreach ($comandas as $comanda)
                            <tr>
                                <td>
                                    <a href="{{ route('sistema.indexAberto', ['card_id' => $comanda->card_id]) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </td>
                                <td>
                                    {{ $comanda->nome }}
                                </td>
                                <td>
                                    {{ date('d/m/Y', strtotime($comanda->updated_at)) }} as
                                    {{ date('H:i:s', strtotime($comanda->updated_at)) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
