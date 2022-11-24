@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-12 text-center">
            <img src="{{ asset('storage/img/logo.png') }}" class="img-fluid">
        </div>
        @if (!empty($porcoes[0]->nome))
            <div class="card p-5 rounded-5" style="background-color:rgb(116, 25, 25); color:white">
                <h1 class="text-center mb-5" style="font-family:menu_itens">PORÇÕES</h1>
                <div class="row">
                    <div class="col-md-6 col-4"></div>
                    <div class="col-md-3 col-4">MEIA</div>
                    <div class="col-md-3 col-4">INTEIRA</div>
                    @foreach ($porcoes as $item)
                        <div class="col-md-6 col-4">
                            {{ $item->nome }}
                        </div>
                        <div class="col-md-3 col-4">
                            R$ {{ number_format($item->valor, 2, ',', '.') }}
                        </div>
                        <div class="col-md-3 col-4">
                            R$ {{ number_format($item->valor * 2, 2, ',', '.') }}
                        </div>
                    @endforeach
                </div>
        @endif
    </div>
    <script>
        $('#body').css("background-image", "url('{{ asset('storage/img/fundo_menu.jpg') }}')")
        $('#body').css("background-repeat", "no-repeat")
        $('#body').css("background-size", "cover")
        $('#body').css("background-position", "fixed")
    </script>
@endsection

@section('style')
    <style>
        @font-face {
            font-family: menu_itens;
            src: url({{ asset('storage/fonts/menu_itens.ttf') }});
        }

        @font-face {
            font-family: sub_menu;
            src: url({{ asset('storage/fonts/submenu.otf') }});
        }
    </style>
@endsection
