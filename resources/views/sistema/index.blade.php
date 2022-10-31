@extends('layouts.app')

@section('content')
    @if ($permitido)
        @include('sistema.modal')
        @include('assets.msg')

        <div class="container">

            <h1>Sistema</h1>
            {{-- Criar form que busca o codigo e retorna a comanda --}}
            <form action="">
                <div class="card container p-3">
                    <div class="typeahead__container">
                        <input id="code" name="code" type="text" class="js-typeahead form-control my-3"
                            autocomplete="off" />
                    </div>
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
            $('#code').focus();
            if (e.key == 'Enter') {
                // chamar comanda após inserir codigo
            }
        })

        $.typeahead({
            input: '.js-typeahead',
            minLength: 3,
            maxItem: 10,
            order: "asc",
            display: "nome",
            source: {
                ajax: {
                    url: "{{ route('search') }}",
                    data: {
                        search: $('#code').val()
                    }
                }
            },
            callback: {
                onClickAfter: function(node, a, item, event) {
                    event.preventDefault();
                    @if (!Session::has('modal'))
                        $('#code').val('');
                        $('#item').val(item.id);
                        $('#qtde').val(prompt('Digite a QUANTIDADE:'));
                        $('#formItem').submit();
                    @else
                        $('#code').val('');
                        $('#itemInit').val(item.id);
                        $('#qtdeInit').val(prompt('Digite a QUANTIDADE:'));
                        $('#modalInitForm').submit();
                    @endif
                }
            }
        });
    </script>
@endsection
