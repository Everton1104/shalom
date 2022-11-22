@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($permitido)
            <h1>Editar Cardápio</h1>
        @else
            <div class="row mb-5">
                <div class="col-sm-12">
                    <h1 id="menu" class="text-center"
                        style="color:orange;font-family:menu;font-size:76px;text-shadow:2px 2px black">
                        Pesqueiro shaloM
                    </h1>
                </div>
                <div class="col-sm-12">
                    <span class="text-center" id="sub-menu"> Bar e Lanchonete </span>
                </div>
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
        @endif
    </div>
    <script>
        $('#body').css("background-image", "url('{{ asset('storage/fundo_menu.jpg') }}')")
        $('#body').css("background-repeat", "no-repeat")
        $('#body').css("background-size", "cover")
        $('#body').css("background-position", "fixed")

        menu = $('#menu').position()
        console.log(window.innerWidth);
        if (window.innerWidth < 750) {
            $('#sub-menu').css("transform", "rotate(0)")
            $('#sub-menu').css("position", "relative")
        } else {
            $('#sub-menu').css("left", window.innerWidth / 2 + menu.left)
            $('#sub-menu').css("top", menu.top + 30)
        }
    </script>
@endsection

@section('style')
    <style>
        @font-face {
            font-family: menu_itens;
            src: url({{ asset('storage/fonts/menu_itens.ttf') }});
        }

        @font-face {
            font-family: menu;
            src: url({{ asset('storage/fonts/menu.otf') }});
        }

        @font-face {
            font-family: sub_menu;
            src: url({{ asset('storage/fonts/submenu.otf') }});
        }

        #sub-menu {
            font-family: sub_menu;
            color: white;
            text-shadow: 2px 2px black;
            font-size: 68px;
            position: absolute;
            transform: rotate(-20deg);
            overflow: hidden;
            white-space: nowrap;
        }
    </style>
@endsection
