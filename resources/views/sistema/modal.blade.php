<div class="modal fade" id="modalInit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="modalInitForm" action="{{ route('sistema.create') }}">
                <div class="modal-header">
                    <h1>{{ isset($cartao->nome) ? 'Alterar Nome' : 'Nova Comanda' }}</h1>
                </div>
                <div class="modal-body">
                    @csrf
                    <h3>Nome</h3>
                    <input class="form-control" name="nome" type="text"
                        value="{{ isset($cartao->nome) ? $cartao->nome : '' }}" />
                    <input class="d-none" name="card_id" type="text"
                        value="{{ isset($cartao->id) ? $cartao->id : '' }}" />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (isset($cartao->nome))
    <div class="modal fade" id="modalAdd" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modalAddForm" method="post" action="{{ route('sistema.store') }}">
                    <div class="modal-header">
                        <h3>Adicionar item para {{ $cartao->nome }}</h3>
                    </div>
                    <div class="modal-body">
                        @csrf
                        @method('POST')
                        <h3>Procurar</h3>
                        <div class="typeahead__container">
                            <input id="procItem" type="text" class="js-typeahead form-control my-3"
                                autocomplete="off" />
                        </div>
                        <input class="d-none" id="itemId" name="id" type="text" />
                        <input class="d-none" id="qtde" name="qtde" type="text" value="1" />
                        <input class="d-none" name="card_id" type="text"
                            value="{{ isset($cartao->id) ? $cartao->id : '' }}" />
                    </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endif


<div class="modal fade" id="addCard" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="modalAddCard" method="post" action="{{ route('sistema.addCard') }}">
                <div class="modal-header">
                    <h3>Cadastrar Novo Cartão</h3>
                </div>
                <div class="modal-body">
                    @csrf
                    @method('POST')
                    <h3>Aproxime o cartão do leitor!</h3>
                    <input class="form-control" id="newcode" name="code" type="number" />
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
