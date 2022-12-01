@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-12 text-center">
            <img src="{{ asset('storage/img/logo.png') }}" class="img-fluid">
        </div>
        <div class="card p-5 rounded-5" style="background-color:rgb(116, 25, 25); color:white">
            @if (!empty($porcoes[0]->nome))
                <h1 class="text-center mb-5" style="font-family:menu_itens">PORÇÕES</h1>
                <div class="row">
                    <div class="col-md-6 col-4"></div>
                    <div class="col-md-3 col-4">MEIA</div>
                    <div class="col-md-3 col-4">INTEIRA</div>
                    @foreach ($porcoes as $item)
                        <div class="col-md-6 col-4">
                            {{ explode(' -', $item->nome)[0] }}
                        </div>
                        <div class="col-md-3 col-4">
                            R$
                            @php
                                foreach ($meia as $itemMeia) {
                                    if ($itemMeia->id == $item->id + 1) {
                                        echo number_format($itemMeia->valor, 2, ',', '.');
                                    }
                                }
                            @endphp
                        </div>
                        <div class="col-md-3 col-4">
                            R$ {{ number_format($item->valor, 2, ',', '.') }}
                        </div>
                    @endforeach
            @endif
            @if (!empty($alcoolicas[0]->nome))
                <h1 class="text-center my-5" style="font-family:menu_itens">BABIDAS ALCOÓLICAS</h1>
                <div class="row">
                    <div class="col-md-6 col-8"></div>
                    <div class="col-md-3 col-4">VALOR</div>
                    @foreach ($alcoolicas as $item)
                        <div class="col-md-6 col-4">
                            {{ $item->nome }}
                        </div>
                        <div class="col-md-3 col-4">
                            R$ {{ number_format($item->valor, 2, ',', '.') }}
                        </div>
                    @endforeach
                </div>
            @endif
            @if (!empty($bebidas[0]->nome))
                <h1 class="text-center my-5" style="font-family:menu_itens">BABIDAS E SUCOS</h1>
                <div class="row">
                    <div class="col-md-6 col-8"></div>
                    <div class="col-md-3 col-4">VALOR</div>
                    @foreach ($bebidas as $item)
                        <div class="col-md-6 col-4">
                            {{ $item->nome }}
                        </div>
                        <div class="col-md-3 col-4">
                            R$ {{ number_format($item->valor, 2, ',', '.') }}
                        </div>
                    @endforeach
                </div>
            @endif
            @if (!empty($doces[0]->nome))
                <h1 class="text-center my-5" style="font-family:menu_itens">DOCES E SOBREMESAS</h1>
                <div class="row">
                    <div class="col-md-6 col-8"></div>
                    <div class="col-md-3 col-4">VALOR</div>
                    @foreach ($doces as $item)
                        <div class="col-md-6 col-4">
                            {{ $item->nome }}
                        </div>
                        <div class="col-md-3 col-4">
                            R$ {{ number_format($item->valor, 2, ',', '.') }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
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
