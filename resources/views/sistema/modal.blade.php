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
                    <input class="form-control" id="nome" name="nome" type="text" />
                    <input class="d-none" id="codeModal" name="code" type="text"
                        value="{{ isset($cartao->nome) ? $cartao->nome : '' }}" />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
