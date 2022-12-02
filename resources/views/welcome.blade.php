@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-12 text-center">
            <img src="{{ asset('storage/img/logo.png') }}" class="img-fluid">
        </div>
        <div class="card p-5 rounded-5" style="background-color:rgb(116, 25, 25); color:white">
            @if (!empty($porcoes[0]->nome))
                <h1 class="text-center mb-5" style="font-family:menu_itens">PORÇÕES</h1>
                <div class="table-responsive">
                    <table class="table" style="color:white">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">MEIA</th>
                                <th scope="col">INTEIRA</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($porcoes as $item)
                                <tr>
                                    <td>
                                        {{ mb_strtoupper(explode(' -', $item->nome)[0]) }}
                                        @if ($item->estoque <= 0)
                                            - ESGOTADO
                                        @endif
                                    </td>
                                    <td class="nowrap">
                                        R$
                                        @php
                                            foreach ($meia as $itemMeia) {
                                                if ($itemMeia->id == $item->id + 1) {
                                                    echo number_format($itemMeia->valor, 2, ',', '.');
                                                }
                                            }
                                        @endphp
                                    </td>
                                    <td class="nowrap">
                                        R$ {{ number_format($item->valor, 2, ',', '.') }}
                                    <td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if (!empty($alcoolicas[0]->nome))
                <h1 class="text-center my-5" style="font-family:menu_itens">BABIDAS ALCOÓLICAS</h1>
                <div class="table-responsive">
                    <table class="table" style="color:white">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alcoolicas as $item)
                                <tr>
                                    <td>
                                        {{ mb_strtoupper($item->nome) }}
                                    </td>
                                    <td class="nowrap">
                                        R$ {{ number_format($item->valor, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if (!empty($bebidas[0]->nome))
                <h1 class="text-center my-5" style="font-family:menu_itens">BABIDAS E SUCOS</h1>
                <div class="table-responsive">
                    <table class="table" style="color:white">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bebidas as $item)
                                <tr>
                                    <td>
                                        {{ mb_strtoupper($item->nome) }}
                                    </td>
                                    <td class="nowrap">
                                        R$ {{ number_format($item->valor, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if (!empty($doces[0]->nome))
                <h1 class="text-center my-5" style="font-family:menu_itens">DOCES E SOBREMESAS</h1>
                <div class="table-responsive">
                    <table class="table" style="color:white">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($doces as $item)
                                <tr>
                                    <td>
                                        {{ mb_strtoupper($item->nome) }}
                                    </td>
                                    <td class="nowrap">
                                        R$ {{ number_format($item->valor, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

        .nowrap {
            white-space: nowrap;
        }
    </style>
@endsection
