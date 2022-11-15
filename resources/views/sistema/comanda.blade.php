@if (isset($cartao->nome))
    <div class="card container p-3 my-5">
        <h1><a style="color:rgb(113, 113, 224); rounded;cursor:pointer" id="btnModal" data-bs-toggle="modal"
                data-bs-target="#modalInit"><i class="fa-solid fa-pen-to-square"></i></a>
            {{ $cartao->nome }}
        </h1>
        <div class="float-end my-3">Data de entrada da comanda
            {{ date('d/m/Y', strtotime($cartao->updated_at)) }}
            as
            {{ date('H:i:s', strtotime($cartao->updated_at)) }}
        </div>
        <div class="float-right">
            <button type="button" class="btn btn-primary btn-sm" onclick="$('#modalAdd').modal('show')">Adicionar
                item</button>
        </div>
        <hr>
        @if (isset($comanda))
            <?php $total = 0; ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Valor unitário</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comanda as $item)
                        @if (empty($item->nome))
                            <tr>
                                <td>{{ $item->obs }}</td>
                                <td></td>
                                <td>{{ $item->qtde }}</td>
                                <td></td>
                            </tr>
                        @else
                            <tr>
                                <td>
                                    <a href="#"
                                        onclick="if(confirm('Deletar {!! $item->qtde . ' ' . $item->nome !!}?')){window.location.href='{!! route('sistema.delete', [$cartao->id, $item->id]) !!}'}">
                                        <i style="color:red;" class="fa-solid fa-trash m-2"></i>
                                    </a>
                                    {{ $item->nome }}
                                </td>
                                <td>R$ {{ number_format($item->valor, 2, ',', '.') }}</td>
                                <td>{{ $item->qtde }}</td>
                                <td>R$ {{ number_format($item->valor * $item->qtde, 2, ',', '.') }}</td>
                                <?php $total += $item->valor * $item->qtde; ?>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <h3>Total Geral: R$ {{ number_format($total, 2, ',', '.') }}</h3>
            <div>
                <a href="#"
                    onclick="if(confirm('Fechar comanda de {!! $cartao->nome !!}?')){window.location.href='{!! route('sistema.pagar', $cartao->id) !!}'}"
                    class="btn btn-success">Pagar</a>
            </div>
        @endif
    </div>
@endif
