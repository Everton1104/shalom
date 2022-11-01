@if (isset($cartao->nome))
    <div class="card container p-3 my-5">
        <h1><a style="color:rgb(113, 113, 224); rounded;cursor:pointer" id="btnModal" data-bs-toggle="modal"
                data-bs-target="#modalInit"><i class="fa-solid fa-pen-to-square"></i></a>
            {{ $cartao->nome }}</h1>
        <hr>
        @if (isset($comanda))
            <?php $total = 0; ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Valor unitário</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comanda as $item)
                        <tr>
                            <td>
                                <a href="#" onclick="if(confirm('Deletar?'))deletar({!! $item->id !!})">
                                    <i style="color:red;" class="fa-solid fa-trash m-2"></i>
                                </a>
                                <a href="#">
                                    <i class="fa-solid fa-pen-to-square m-2"></i>
                                </a>
                                {{ $item->nome }}
                            </td>
                            <td>R$ {{ $item->valor }}</td>
                            <td>{{ $item->qtde }}</td>
                            <td>R$ {{ $item->valor * $item->qtde }}</td>
                            <?php $total += $item->valor * $item->qtde; ?>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <h3>Total Geral: R$ {{ $total }}</h3>
        @endif
    </div>
    <script>
        function deletar(id) {
            location.href = "sistema/" + id
        }
    </script>
@endif
