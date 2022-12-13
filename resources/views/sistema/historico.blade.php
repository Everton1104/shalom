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
                    <select class="form-select" name="userId">
                        <option selected disabled>Selecione o usuário</option>
                        @foreach ($users as $usersItem)
                            <option value="{{ $usersItem->id }}">{{ $usersItem->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select" name="operacao">
                        <option selected disabled>Filtrar tipo de operação</option>
                        <option value="0">Todas</option>
                        <option value="1">Criou a Comanda</option>
                        <option value="2">Adicionou itens na comanda</option>
                        <option value="3">Removeu itens da comanda</option>
                        <option value="4">Registrou pagamentos</option>
                        <option value="5">Cadastrou cartões</option>
                        <option value="6">Cadastrou novos produtos</option>
                        <option value="7">Excluiu produtos</option>
                        <option value="8">Alterou produtos</option>
                        <option value="9">Adicionou produtos no estoque</option>
                        <option value="10">Removeu produtos do estoque</option>
                        <option value="11">Perda de produtos</option>
                        <option value="12">Bonificação de produtos</option>
                    </select>
                </div>
            </form>

            @if (isset($user->name))
                <h2>Histórico de {{ $user->name }}</h2>
                <div class="text-nowrap">
                    @foreach ($historico as $item)
                        {{ date('d/m/Y', strtotime($item->updated_at)) . ' ás ' . date('H:i:s', strtotime($item->updated_at)) }}
                        -> {{ $item->obs }}<br>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="container">
            <h1>Acesso negado para o usuário: {{ Auth::user()->name }}</h1>
        </div>
    @endif
@endsection
