@if (isset($cartao))
    <div class="card container p-3 my-5">
        <h1><a style="color:rgb(113, 113, 224); rounded;cursor:pointer" id="btnModal" data-bs-toggle="modal"
                data-bs-target="#modalInit"><i class="fa-solid fa-pen-to-square"></i></a>
            {{ $cartao->nome ?? 'sem nome' }}</h1>
        <hr>
    </div>
@endif
