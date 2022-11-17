<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComandaModel;
use App\Models\ItemModel;
use App\Models\CartaoModel;
use App\Models\EstoqueModel;

class SistemaController extends Controller
{
    public function permissao($id)
    { // Lista de id's permitidos
        switch ($id) {
            case '1':
                return true;
            case '2':
                return true;
            default:
                return false;
        }
    }

    public function requestAPI(array $dados = ['method' => 'GET', 'url' => '', 'conteudo' => ['']])
    {
        $context  = stream_context_create(
            array(
                'http' =>
                array(
                    'method'  => $dados['method'],
                    'header'  => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query($dados['conteudo'])
                )
            )
        );
        return file_get_contents($dados['url'], false, $context);
    }

    // $response = $this->requestAPI([
    //     'method' => 'POST',
    //     'url' => 'https://www.google.com/recaptcha/api/siteverify',
    //     'conteudo' => [
    //         'secret' => '6LdbLIsiAAAAAAiXwe4wlLiQIeBnqpC2ujnSktGl',
    //         'response' => $request["g-recaptcha-response"]
    //     ],
    // ]);
    // if (json_decode($response)->success) {

    public function searchItem(Request $request)
    {
        if (isset($request)) {
            return json_encode(ItemModel::where('nome', 'LIKE', '%' . $request->search . '%')->get()->toArray());
        } else {
            return json_encode(ItemModel::get()->toArray());
        }
    }

    public function searchComanda(Request $request)
    {
        if (isset($request->code)) {
            $cartao = CartaoModel::where('code', '=', $request->code)->first();
            if (isset($cartao->id)) {
                return $this->index($cartao->id);
            }
        }
        return $this->index();
    }

    public function searchNome(Request $request)
    {
        if (isset($request->nome)) {
            $cartao = CartaoModel::where('nome', 'LIKE', '%' . $request->nome . '%')->first();
            if (isset($cartao->id)) {
                return $this->index($cartao->id);
            }
        }
        return $this->index();
    }

    public function searchNomeAberto(Request $request)
    {
        if (isset($request->nome)) {
            $comandas = ComandaModel::where([
                ['pago', '0'],
                ['nome', 'LIKE', '%' . $request->nome . '%']
            ])
                ->leftJoin(
                    'cartao',
                    'comanda.card_id',
                    '=',
                    'cartao.id'
                )
                ->select('comanda.*', 'cartao.nome', 'cartao.updated_at')
                ->groupBy('card_id')->get();
            $permitido = $this->permissao(Auth::user()->id);
            return view('sistema.aberto', compact('permitido', 'comandas'));
        }
        return $this->aberto();
    }

    public function aberto()
    {
        $comandas = ComandaModel::where('pago', '0')
            ->leftJoin(
                'cartao',
                'comanda.card_id',
                '=',
                'cartao.id'
            )
            ->select('comanda.*', 'cartao.nome')
            ->groupBy('card_id')->get();
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.aberto', compact('permitido', 'comandas'));
    }

    public function indexAberto(Request $request)
    {
        if (isset($request->card_id)) {
            return $this->index($request->card_id);
        }
        return $this->index();
    }

    public function index($card_id = null)
    {
        if (isset($card_id)) {
            $comanda = ComandaModel::where(
                [
                    ['card_id', '=', $card_id],
                    ['pago', '=', '0'],
                ]
            )->leftJoin(
                'itens',
                'comanda.item_id',
                '=',
                'itens.id'
            )
                ->select('comanda.*', 'itens.nome', 'itens.valor', 'itens.id as itemId', 'categoria')
                ->get();
            $cartao = CartaoModel::where('id', '=', $card_id)->first();
            $permitido = $this->permissao(Auth::user()->id);
            return view('sistema.index', compact('permitido', 'cartao', 'comanda'));
        }
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.index', compact('permitido'));
    }

    public function create(Request $request)
    {
        if (isset($request->card_id)) {
            $cartao = CartaoModel::where('id', $request->card_id)->first() ?? false;
            if ($cartao) {
                $cartao->update(['nome' => $request->nome]);
                return $this->index($cartao->id);
            }
        } else {
            return redirect()->back()->with('erroMsg', 'Codigo não encontrado.');
        }
    }

    public function store(Request $request)
    {
        if (isset($request->id) && isset($request->card_id)) {
            ComandaModel::create(
                [
                    'item_id' => $request->id,
                    'card_id' => $request->card_id,
                    'qtde' => $request->qtde,
                ]
            );
            $estoque = EstoqueModel::where('item_id', $request->id)->first();
            EstoqueModel::where('item_id', $request->id)->update([
                'qtde' => $estoque->qtde - $request->qtde,
            ]);
            return $this->index($request->card_id);
        } else {
            if (isset($request->card_id)) {
                return $this->index($request->card_id);
            } else {
                return $this->index();
            }
        }
    }

    public function show($id)
    {
        return 'show';
    }

    public function deletar($card, $id = null)
    {
        if (isset(ComandaModel::where('id', $id)->first()->id)) {
            $comanda = ComandaModel::where('id', $id)->first();
            $estoque = EstoqueModel::where('item_id', $comanda->item_id)->first();
            EstoqueModel::where('item_id', $comanda->item_id)->update([
                'qtde' => $estoque->qtde + $comanda->qtde
            ]);
            $comanda->delete();
        }
        return $this->index($card);
    }

    public function pagar(Request $request)
    {
        if (isset(ComandaModel::where('card_id', $request->card)->first()->id)) {
            ComandaModel::where([
                ['card_id', $request->card],
                ['pago', '0'],
            ])->update([
                'pago' => 1,
                'tipo' => $request->tipo,
                'nome' => $request->nome,
            ]);
            CartaoModel::where('id', $request->card)->update(['nome' => null]);
            return redirect()->back()->with('msg', 'Comanda paga.');
        }
        return redirect()->back()->with('erroMsg', 'Comanda vazia.');
    }

    public function addCard(Request $request)
    {
        if (!empty($request->code)) {
            try {
                $cartao = CartaoModel::create(['code' => $request->code]);
                return redirect()->back()->with('msg', 'Cartão ' . $cartao->code . ' criado.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('erroMsg', 'Este cartão já existe.');
            }
        }
        return redirect()->back()->with('erroMsg', 'Valor inválido');
    }

    public function indexProdutos()
    {
        $itens = ItemModel::leftJoin(
            'estoque',
            'estoque.item_id',
            '=',
            'itens.id'
        )
            ->select('itens.*', 'estoque.valor as valorCompra', 'estoque.qtde')->get();
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.produtos', compact('permitido', 'itens'));
    }

    public function novoProduto(Request $request)
    {
        if (!empty($request->nome) && !empty($request->valor) && !empty($request->valorCompra) && !empty($request->categoria) && !empty($request->qtde)) {
            $item = ItemModel::create([
                'nome' => $request->nome,
                'valor' => $request->valor,
                'categoria' => $request->categoria,
            ]);
            $estoque = EstoqueModel::create([
                'item_id' => $item->id,
                'valor' => $request->valorCompra,
                'qtde' => $request->qtde
            ]);
            ItemModel::where('id', $item->id)->update([
                'estoque_id' => $estoque->id
            ]);
            return redirect()->back()->with('msg', 'Adicionado');
        } else {
            return redirect()->back()->with('erroMsg', 'Valores inválidos');
        }
    }

    public function deleteProduto($id)
    {
        try {
            $item = ItemModel::where('id', $id)->first();
            ComandaModel::where('item_id', $id)->update([
                'obs' => 'O produto ' . $item->nome . ' foi deletado por ' . Auth::user()->name . ' em ' . date('d/m/Y') . ' as ' . date('H:i:s') . ' valor un. R$' . number_format($item->valor, 2, ',', '.')
            ]);
            ItemModel::where('id', $id)->delete();
            EstoqueModel::where('item_id', $id)->delete();
            return redirect()->back()->with('msg', $item->nome . " apagado.");
        } catch (\Throwable $th) {
            return redirect()->back()->with('erroMsg', "Erro ao apagar" . $th);
        }
    }

    public function editarProduto(Request $request)
    {
        if (!empty($request->nome) && !empty($request->valor) && !empty($request->id) && !empty($request->valorCompra) && !empty($request->categoria)) {
            ItemModel::where('id', $request->id)->update([
                'nome' => $request->nome,
                'valor' => $request->valor,
                'categoria' => $request->categoria,
            ]);
            EstoqueModel::where('item_id', $request->id)->update([
                'valor' => $request->valorCompra,
            ]);
            return redirect()->back()->with('msg', "Atualizado.");
        }
        return redirect()->back()->with('erroMsg', "Erro ao Atualizar");
    }

    public function indexEstoque(Request $request)
    {
        if (isset($request->buscar)) {
            $itens = EstoqueModel::where('itens.nome', 'LIKE', '%' . $request->buscar . '%')
                ->leftJoin(
                    'itens',
                    'estoque.item_id',
                    '=',
                    'itens.id'
                )
                ->select('estoque.*', 'itens.nome', 'itens.valor as valorVenda')
                ->get();
        } else {
            $itens = EstoqueModel::leftJoin(
                'itens',
                'estoque.item_id',
                '=',
                'itens.id'
            )
                ->select('estoque.*', 'itens.nome', 'itens.valor as valorVenda')->get();
        }
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.estoque', compact('permitido', 'itens'));
    }

    public function addEstoque(Request $request)
    {
        if (!empty($request->qtde) && !empty($request->item_id)) {
            $estoque = EstoqueModel::where('item_id', $request->item_id)->first();
            EstoqueModel::where('item_id', $request->item_id)->update([
                'qtde' => $estoque->qtde + $request->qtde,
                'user' => Auth::user()->nome,
                'obs' => Auth::user()->name . ' registrou a entrada de ' . $request->qtde . ' destes produtos em ' . date('d/m/Y') . ' as ' . date('H:i:s'),
            ]);
            return redirect()->back()->with('msg', 'Adicionado ao estoque');
        }
        return redirect()->back()->with('erroMsg', 'Erro ao adicionar estoque');
    }

    public function removeEstoque(Request $request)
    {
        if (!empty($request->qtde) && !empty($request->item_id)) {
            $estoque = EstoqueModel::where('item_id', $request->item_id)->first();
            EstoqueModel::where('item_id', $request->item_id)->update([
                'qtde' => $estoque->qtde - $request->qtde,
                'user' => Auth::user()->nome,
                'obs' => Auth::user()->name . ' registrou a remoção de ' . $request->qtde . ' destes produtos em ' . date('d/m/Y') . ' as ' . date('H:i:s'),
            ]);
            return redirect()->back()->with('msg', 'Removido do estoque');
        }
        return redirect()->back()->with('erroMsg', 'Erro ao remover estoque');
    }

    public function extravio(Request $request)
    {
        if (!empty($request->qtde) && !empty($request->item_id)) {
            ComandaModel::create(
                [
                    'item_id' => $request->item_id,
                    'card_id' => 999,
                    'qtde' => $request->qtde,
                    'obs' => Auth::user()->name . ' registrou a perda deste(es) produto(os) em ' . date('d/m/Y') . ' as ' . date('H:i:s'),
                ]
            );
            $estoque = EstoqueModel::where('item_id', $request->item_id)->first();
            EstoqueModel::where('item_id', $request->item_id)->update([
                'qtde' => $estoque->qtde - $request->qtde,
            ]);
            return redirect()->back()->with('msg', 'Produto(os) extraviado(os).');
        }
        return redirect()->back()->with('erroMsg', 'Erro ao cadastrar.');
    }

    public function bonificacao(Request $request)
    {
        if (!empty($request->qtde) && !empty($request->item_id) && !empty($request->code)) {
            $card = CartaoModel::where('code', $request->code)->first();
            ComandaModel::create(
                [
                    'item_id' => $request->item_id,
                    'card_id' => 888,
                    'nome' => $card->nome,
                    'qtde' => $request->qtde,
                    'obs' => Auth::user()->name . ' registrou a bonificação deste(es) produto(os) em ' . date('d/m/Y') . ' as ' . date('H:i:s'),
                ]
            );
            $estoque = EstoqueModel::where('item_id', $request->item_id)->first();
            EstoqueModel::where('item_id', $request->item_id)->update([
                'qtde' => $estoque->qtde - $request->qtde,
            ]);
            return redirect()->to('searchComanda?code=' . $request->code)->with('msg', 'Produto(os) bonificado(os).');
        }
        return redirect()->back()->with('erroMsg', 'Erro ao cadastrar.');
    }

    public function alterarSenha(Request $request)
    {
        $user = Auth::user();
        $user->password = bcrypt($request->pass);
        $user->save();
        return redirect()->back()->with('msg', "Senha Alterada");
    }

    public function searchProduto(Request $request)
    {
        if (isset($request->search)) {
            $itens = ItemModel::where('nome', 'LIKE', '%' . $request->search . '%')->get();
            $permitido = $this->permissao(Auth::user()->id);
            return view('sistema.produtos', compact('permitido', 'itens'));
        }
        return $this->indexProdutos();
    }

    public function indexRelatorio(Request $request)
    {
        if (isset($request->dataInit) && isset($request->dataFim)) {
            if (strtotime($request->dataInit) <= strtotime($request->dataFim)) {
                $resumo = ComandaModel::whereBetween(
                    'comanda.updated_at',
                    [date('Y-m-d', strtotime($request->dataInit)), date('Y-m-d', strtotime('+1 days', strtotime($request->dataFim)))]
                )
                    ->where('pago', '1')
                    ->leftJoin(
                        'itens',
                        'comanda.item_id',
                        '=',
                        'itens.id'
                    )
                    ->select('comanda.*', 'itens.nome as itemNome', 'itens.valor', 'itens.id as itemId')->get();
                $permitido = $this->permissao(Auth::user()->id);
                return view('sistema.relatorio', compact('permitido', 'resumo', 'request'));
            } else {
                return redirect()->back()->with('erroMsg', 'A data de inicio não pode ser maior do que a final.');
            }
        }
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.relatorio', compact('permitido'));
    }
}
