<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />

    <!-- Typeahead style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.css" />

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.js"></script>
    @yield('style')
    @yield('scriptTop')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body id="body">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Entrar') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Registrar-se') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if ($permitido)
                                        <a class="dropdown-item" href="{{ route('sistema.index') }}">
                                            {{ __('Sistema') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('sistema.cadastroProdutos') }}">
                                            {{ __('Cadastro de produtos') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('sistema.estoque') }}">
                                            {{ __('Estoque') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('sistema.relatorio') }}">
                                            {{ __('Relatórios') }}
                                        </a>
                                        @if (Auth::user()->id == 1 || Auth::user()->id == 2)
                                            <a class="dropdown-item" href="{{ route('sistema.historico') }}">
                                                {{ __('Histórico') }}
                                            </a>
                                        @endif
                                    @endif
                                    <a class="dropdown-item" href="#" onclick="$('#modalAltPass').modal('show')">
                                        {{ __('Alterar Senha') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Sair') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <div class="modal fade" id="modalAltPass" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formAltPass" method="post" action="{{ route('altPass') }}">
                        <div class="modal-header">
                            <h3>Alterar Senha</h3>
                        </div>
                        <div class="modal-body">
                            @csrf
                            @method('POST')
                            <label for="pass">Nova Senha</label>
                            <div class="row">
                                <div class="col-11">
                                    <input class="form-control" id="pass" name="pass" type="password" />
                                </div>
                                <div class="col-1 d-flex align-items-center">
                                    <a
                                        onclick="if($('#pass').attr('type') == 'password'){
                                        $('#pass').attr('type','text') && $('#eye').attr('class','fa-solid fa-eye-slash')
                                        }else{
                                            $('#pass').attr('type','password') && $('#eye').attr('class','fa-solid fa-eye')
                                        }">
                                        <i id="eye" class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="$('#formAltPass').submit()">Enviar</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @yield('scriptEnd')
</body>

</html>
