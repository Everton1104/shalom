@if (Session::has('erroMsg'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h3>{{ Session::get('erroMsg') }}</h3>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" id="fechar-alerta"></button>
    </div>
@elseif(Session::has('msg'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h3>{{ Session::get('msg') }}</h3>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" id="fechar-alerta"></button>
    </div>
@endif

<script>
    @if (Session::has('erroMsg'))
        setTimeout(() => {
            $('#fechar-alerta').click();
        }, 10000);
    @elseif (Session::has('msg'))
        setTimeout(() => {
            $('#fechar-alerta').click();
        }, 3000);
    @endif
</script>
