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
        <div>
            <button type="button" class="btn btn-primary btn-sm" onclick="$('#modalAdd').modal('show')">Adicionar
                item</button>
            <div class="mx-3 float-end">
                <button type="button" class="btn btn-warning btn-sm"
                    onclick="$('#modalBonificacao').modal('show') && $('#nomeBonificacao').text('{!! $cartao->nome !!}')">
                    Bonificação
                </button>
            </div>
        </div>
        <hr>
        @if (isset($comanda))
            <?php $total = 0; ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Categoria</th>
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
                                <td>
                                    @php
                                        switch ($item->categoria) {
                                            case '1':
                                                echo 'Bebidas Alcoólicas';
                                                break;
                                            case '2':
                                                echo 'Porções';
                                                break;
                                            case '3':
                                                echo 'Bebidas';
                                                break;
                                            case '4':
                                                echo 'Doces e Sobremesas';
                                                break;
                                        }
                                    @endphp
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
                <a href="#" onclick="$('#modalPagar').modal('show')" class="btn btn-success">Pagar</a>
            </div>
            <div class="modal fade" id="modalPagar" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="needs-validation" novalidate id="formPagar" method="post"
                            action="{{ route('sistema.pagar') }}">
                            <div class="modal-header">
                                <h3>Fechar Comanda de {!! $cartao->nome !!}</h3>
                            </div>
                            <div class="modal-body">
                                @csrf
                                @method('POST')
                                <input type="text" class="d-none" name="card" value="{{ $cartao->id }}" />
                                <input type="text" class="d-none" name="nome" value="{{ $cartao->nome }}" />
                                <h3>Selecione o tipo de pagamento</h3>
                                <select class="form-select" name="tipo" required>
                                    <option value="" selected disabled>Selecione uma opção</option>
                                    <option value="1">Débito</option>
                                    <option value="2">Crédito</option>
                                    <option value="3">PIX</option>
                                    <option value="4">Dinheiro</option>
                                </select>
                                <div class="my-3">
                                    Total Geral: R$ {{ number_format($total, 2, ',', '.') }}
                                </div>
                            </div>
                            <button type="submit" id="btnPagar" class="d-none"></button>
                        </form>
                        <div class="modal-footer">
                            <button class="btn btn-success" onclick="$('#btnPagar').click()">Pagar</button>
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                (() => {
                    'use strict'
                    const forms = document.querySelectorAll('.needs-validation')
                    Array.from(forms).forEach(form => {
                        form.addEventListener('submit', event => {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }
                            form.classList.add('was-validated')
                        }, false)
                    })
                })()
            </script>
        @endif
    </div>
@endif
{{-- onclick="if(confirm('Fechar comanda de {!! $cartao->nome !!}?')){window.location.href='{!! route('sistema.pagar', $cartao->id) !!}'}" --}}
