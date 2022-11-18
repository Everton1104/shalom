@extends('layouts.app')

@section('content')
    @if ($permitido)
        @include('sistema.modal')
        @include('assets.msg')

        <div class="container">
            <div class="mb-5 row text-center">
                @if (Auth::user()->id == 1 || Auth::user()->id == 2)
                    <div class="col m-2">
                        <button class="btn btn-success" onclick="$('#addCard').modal('show')">Cadastrar Cartão</button>
                    </div>
                @endif
                <div class="col m-2">
                    <a class="btn btn-primary" href="{{ route('sistema.aberto') }}">Procurar por Nome</a>
                </div>
                <div class="align-end col m-2">
                    <button type="button" class="btn btn-danger" onclick="$('#modalExtravio').modal('show')">
                        Extravio
                    </button>
                </div>
            </div>
            <h1>Sistema</h1>
            <form action="{{ route('searchComanda') }}" method="POST" id="searchComanda">
                @csrf
                @method ('POST')
                <div class="my-3">
                    <input type="text" class="form-control" name="code" id="code" autocomplete="off" />
                </div>
            </form>
            @include('sistema.comanda')
        </div>
    @else
        <div class="container">
            <h1>Acesso negado para o usuário: {{ Auth::user()->name }}</h1>
        </div>
    @endif
@endsection

@section('scriptEnd')
    <script>
        window.addEventListener('keydown', (e) => {
            if ($('#modalAdd').hasClass('show')) {
                $('#procItem').focus()
            } else if ($('#addCard').hasClass('show')) {
                $('#newcode').focus()
            } else if ($('#modalExtravio').hasClass('show')) {
                $('#procItemExtravio').focus()
            } else if ($('#modalBonificacao').hasClass('show')) {
                $('#procItemBonificacao').focus()
            } else {
                $('#code').focus();
                if (e.key == 'Enter') {
                    $('#searchComanda').submit()
                }
                if (e.key == 'Control') {
                    $('#modalAdd').modal('show')
                }
            }
        })

        @if (isset($cartao))
            @if (!isset($cartao->nome))
                $(document).ready(function() {
                    $('#modalInit').modal('show')
                })
            @endif
        @endif

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
                    if ($('#modalExtravio').hasClass('show')) {
                        event.preventDefault();
                        qtde = prompt("Digite a QUANTIDADE:")
                        $('#item_idExtravio').val(item.id)
                        $('#qtdeExtravio').val(qtde)
                        $('#formExtravio').submit()
                    } else if ($('#modalBonificacao').hasClass('show')) {
                        event.preventDefault();
                        qtde = prompt("Digite a QUANTIDADE:")
                        $('#item_idBonificacao').val(item.id)
                        $('#qtdeBonificacao').val(qtde)
                        $('#nomeValBonificacao').val("{{ $cartao->code ?? '' }}")
                        $('#formBonificacao').submit()
                    } else {
                        event.preventDefault();
                        qtde = prompt("Digite a QUANTIDADE:")
                        $('#itemId').val(item.id)
                        $('#qtde').val(qtde)
                        $('#modalAddForm').submit()
                    }
                }
            }
        });
    </script>
@endsection
