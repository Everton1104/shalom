@extends('layouts.app')

@section('content')
    @if ($permitido)
        @include('sistema.modal')
        @include('assets.msg')

        <div class="container">
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
