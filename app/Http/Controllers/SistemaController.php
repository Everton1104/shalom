<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComandaModel;
use App\Models\ItemModel;
use App\Models\CartaoModel;
use App\Models\EstoqueModel;
use App\Models\HistoricoModel;

class SistemaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function permissao($id)
    {
        $permitidos = explode(',', env('PERMITIDO'));

        foreach ($permitidos as $permitido) {
            if ($permitido == $id) {
                return true;
            }
        }
        return false;
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
            if ($request->code != 888 && $request->code != 999) {
                $cartao = CartaoModel::where('code', '=', $request->code)->first();
                if (isset($cartao->id)) {
                    return $this->index($cartao->id);
                }
            } else {
                return redirect()->to('sistema')->with('erroMsg', 'Cartão indisponivel');
            }
        }
        return redirect()->to('sistema')->with('erroMsg', 'Cartão não encontrado.');
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
                ['cartao.nome', 'LIKE', '%' . $request->nome . '%']
            ])
                ->leftJoin(
                    'cartao',
                    'comanda.card_id',
                    '=',
                    'cartao.id'
                )
                ->select('comanda.*', 'comanda.nome as comandaNome', 'cartao.nome', 'cartao.updated_at')
                ->groupBy('card_id')->get();
            $permitido = $this->permissao(Auth::user()->id);
            return view('sistema.aberto', compact('permitido', 'comandas'));
        }
        return $this->aberto();
    }

    public function aberto()
    {
        $comandas = ComandaModel::where([
            ['pago', '0'],
            ['comanda.card_id', '!=', 888],
            ['comanda.card_id', '!=', 999]
        ])
            ->leftJoin(
                'cartao',
                'comanda.card_id',
                '=',
                'cartao.id'
            )
            ->select('comanda.*', 'comanda.nome as comandaNome', 'cartao.nome')
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
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 1,
                'obs' => Auth::user()->name . ' alterou o nome ' . $cartao->nome . ' do cartao ' . $cartao->id . ' para ' . $request->nome
            ]);
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
        if (isset($request->id) && isset($request->card_id) && isset($request->qtde)) {
            $item = ItemModel::where('id', $request->id)->first();
            $comanda = ComandaModel::create(
                [
                    'item_id' => $request->id,
                    'card_id' => $request->card_id,
                    'qtde' => $request->qtde,
                    'registro' => "Registrado por " . Auth::user()->name . " no valor de R$ " . number_format($item->valor, 2, ',', '.')
                ]
            );
            $estoque = EstoqueModel::where('item_id', $request->id)->first();
            EstoqueModel::where('item_id', $request->id)->update([
                'qtde' => $estoque->qtde - $request->qtde,
            ]);
            $cartao = CartaoModel::where('id', $comanda->card_id)->first();
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 2,
                'obs' => Auth::user()->name . ' criou o pedido ' . $comanda->id . ' de ' . $cartao->nome
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
            $cartao = CartaoModel::where('id', $comanda->card_id)->first();
            $estoque = EstoqueModel::where('item_id', $comanda->item_id)->first();
            $item = ItemModel::where('id', $comanda->item_id)->first();
            EstoqueModel::where('item_id', $comanda->item_id)->update([
                'qtde' => $estoque->qtde + $comanda->qtde
            ]);
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 3,
                'obs' => Auth::user()->name . ' removeu ' . $comanda->qtde . ' X ' . $item->nome . ' no valor un. de R$ ' . number_format($item->valor, 2, ',', '.') . ' da comanda de ' . $cartao->nome
            ]);
            $comanda->delete();
        }
        return $this->index($card);
    }

    public function pagar(Request $request)
    {
        if (isset(ComandaModel::where('card_id', $request->card)->first()->id)) {
            $comanda = ComandaModel::where([
                ['card_id', $request->card],
                ['pago', '0'],
            ])->update([
                'pago' => 1,
                'tipo' => $request->tipo,
                'nome' => $request->nome,
            ]);
            $cartao = CartaoModel::where('id', $request->card)->first();
            CartaoModel::where('id', $request->card)->update(['nome' => null]);
            switch ($request->tipo) {
                case '1':
                    $tipo = 'Débito';
                    break;
                case '2':
                    $tipo = 'Crédito';
                    break;
                case '3':
                    $tipo = 'PIX';
                    break;
                case '4':
                    $tipo = 'Dinheiro';
                    break;
            }
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 4,
                'obs' => Auth::user()->name . ' registrou o pagamento via ' . $tipo . ' no valor de R$ ' . number_format($request->total, 2, ',', '.') . ' da comanda de ' . $cartao->nome
            ]);
            return redirect()->back()->with('msg', 'Comanda paga.');
        }
        return redirect()->back()->with('erroMsg', 'Comanda vazia.');
    }

    public function addCard(Request $request)
    {
        if (Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 7) {
            if (!empty($request->code)) {
                try {
                    $cartao = CartaoModel::create(['code' => $request->code]);
                    HistoricoModel::create([
                        'user_id' => Auth::user()->id,
                        'operacao' => 5,
                        'obs' => Auth::user()->name . ' cadastrou um novo cartão: ' . $cartao->code
                    ]);
                    return redirect()->back()->with('msg', 'Cartão ' . $cartao->code . ' criado.');
                } catch (\Throwable $th) {
                    return redirect()->back()->with('erroMsg', 'Este cartão já existe.');
                }
            }
            return redirect()->back()->with('erroMsg', 'Valor inválido');
        } else {
            return redirect()->back()->with('erroMsg', Auth::user()->name . ' não tem permissão para cadastrar.');
        }
    }

    public function indexProdutos()
    {
        $itens = ItemModel::leftJoin(
            'estoque',
            'estoque.item_id',
            '=',
            'itens.id'
        )
            ->select('itens.*', 'estoque.valor as valorCompra', 'estoque.qtde as qtdeEstoque')->orderBy('id')->get();
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.produtos', compact('permitido', 'itens'));
    }

    public function novoProduto(Request $request)
    {
        if (!empty($request->nome) && !empty($request->valor) && !empty($request->valorCompra) && !empty($request->categoria) && !empty($request->qtde)) {
            if ($request->categoria == 2 && !empty($request->valorMeia) && !empty($request->qtdeMeia) && !empty($request->qtdeInt)) {
                $item = ItemModel::create([
                    'nome' => $request->nome . " - INTEIRA",
                    'valor' => $request->valor,
                    'qtde' => $request->qtdeInt,
                    'categoria' => $request->categoria,
                ]);
                $itemMeia = ItemModel::create([
                    'nome' => $request->nome . " - MEIA",
                    'valor' => $request->valorMeia,
                    'qtde' => $request->qtdeMeia,
                    'categoria' => 5,
                ]);
                $estoque = EstoqueModel::create([
                    'item_id' => $item->id,
                    'valor' => $request->valorCompra,
                    'qtde' => $request->qtde,
                    'user' => Auth::user()->name,
                    'obs' => Auth::user()->name . " criou " . $request->nome . " com " . $request->qtde . " unidades ou kg em " . date('d/m/Y') . ' as ' . date('H:i:s') . ' valor un. R$' . number_format($request->valorCompra, 2, ',', '.'),
                ]);
                $item->update([
                    'estoque_id' => $estoque->id
                ]);
                $itemMeia->update([
                    'estoque_id' => $estoque->id
                ]);
                HistoricoModel::create([
                    'user_id' => Auth::user()->id,
                    'operacao' => 6,
                    'obs' => Auth::user()->name . ' cadastrou um novo produto: ' . $request->qtde . ' X ' . $request->nome . ' no valor de venda de R$ ' . number_format($request->valor, 2, ',', '.') . ' e compra R$ ' . number_format($request->valorCompra, 2, ',', '.') . ' a unidade.'
                ]);
                return redirect()->back()->with('msg', 'Adicionado');
            }
            $item = ItemModel::create([
                'nome' => $request->nome,
                'valor' => $request->valor,
                'categoria' => $request->categoria,
            ]);
            $estoque = EstoqueModel::create([
                'item_id' => $item->id,
                'valor' => $request->valorCompra,
                'qtde' => $request->qtde,
                'user' => Auth::user()->name,
                'obs' => Auth::user()->name . " criou " . $request->nome . " com " . $request->qtde . " unidades em " . date('d/m/Y') . ' as ' . date('H:i:s') . ' valor un. R$' . number_format($request->valorCompra, 2, ',', '.'),
            ]);
            ItemModel::where('id', $item->id)->update([
                'estoque_id' => $estoque->id
            ]);
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 6,
                'obs' => Auth::user()->name . ' cadastrou um novo produto: ' . $request->qtde . ' X ' . $request->nome . ' no valor de venda de R$ ' . number_format($request->valor, 2, ',', '.') . ' e compra R$ ' . number_format($request->valorCompra, 2, ',', '.') . ' a unidade.'
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
            if ($item->categoria == 2) {
                $itemMeia = ItemModel::where('id', $id + 1)->first();
                $itemMeia->delete();
            }
            ComandaModel::where('item_id', $id)->update([
                'obs' => 'O produto ' . $item->nome . ' foi deletado por ' . Auth::user()->name . ' em ' . date('d/m/Y') . ' as ' . date('H:i:s') . ' valor un. R$' . number_format($item->valor, 2, ',', '.')
            ]);
            ItemModel::where('id', $id)->delete();
            EstoqueModel::where('item_id', $id)->delete();
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 7,
                'obs' => Auth::user()->name . ' apagou o produto ' . $item->nome . ' valor un. R$' . number_format($item->valor, 2, ',', '.')
            ]);
            return redirect()->back()->with('msg', $item->nome . " apagado.");
        } catch (\Throwable $th) {
            return redirect()->back()->with('erroMsg', "Erro ao apagar" . $th);
        }
    }

    public function editarProduto(Request $request) // deletar item de meia porcao quando trocar a categoria e tambem quando deletar normal
    {
        if (!empty($request->nome) && !empty($request->valor) && !empty($request->id)) {
            $item = ItemModel::where('id', $request->id)->first();
            $estoque = EstoqueModel::where('id', $item->estoque_id)->first();
            ItemModel::where('id', $request->id)->update([
                'nome' => $request->nome,
                'valor' => $request->valor,
                'qtde' => $request->qtde,
                'categoria' => $request->categoria ?? $item->categoria,
            ]);
            if ($request->categoria == 2) {
                $item->update([
                    'nome' => $request->nome . " - INTEIRA",
                    'qtde' => $request->qtdeInt,
                ]);
            }
            if ($item->categoria == 5) {
                $item->update([
                    'qtde' => $request->qtdeMeia,
                ]);
            }
            if ($request->categoria != 5) {
                EstoqueModel::where('item_id', $request->id)->update([
                    'valor' => $request->valorCompra,
                ]);
            }
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 8,
                'obs' => Auth::user()->name . ' alterou o produto ' . $item->nome . ' valor un. R$' . number_format($item->valor, 2, ',', '.') . ' valor de compra un. R$' . number_format($estoque->valor, 2, ',', '.') . ' categoria ' . $item->categoria . ' para: ' .
                    $request->nome . ' valor un. R$' . number_format($request->valor, 2, ',', '.') . ' valor de compra un. R$' . number_format($request->valorCompra, 2, ',', '.') . ' categoria ' . $request->categoria
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
            $item = ItemModel::where('id', $request->item_id)->first();
            EstoqueModel::where('item_id', $request->item_id)->update([
                'qtde' => $estoque->qtde + $request->qtde,
                'user' => Auth::user()->name,
                'obs' => Auth::user()->name . ' registrou a entrada de ' . $request->qtde . ' destes produtos em ' . date('d/m/Y') . ' as ' . date('H:i:s'),
            ]);
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 9,
                'obs' => Auth::user()->name . ' registrou a entrada de ' . $request->qtde . ' unidades de ' . $item->nome
            ]);
            return redirect()->back()->with('msg', 'Adicionado ao estoque');
        }
        return redirect()->back()->with('erroMsg', 'Erro ao adicionar estoque');
    }

    public function removeEstoque(Request $request)
    {
        if (!empty($request->qtde) && !empty($request->item_id)) {
            $estoque = EstoqueModel::where('item_id', $request->item_id)->first();
            $item = ItemModel::where('id', $request->item_id)->first();
            EstoqueModel::where('item_id', $request->item_id)->update([
                'qtde' => $estoque->qtde - $request->qtde,
                'user' => Auth::user()->nome,
                'obs' => Auth::user()->name . ' registrou a remoção de ' . $request->qtde . ' destes produtos em ' . date('d/m/Y') . ' as ' . date('H:i:s'),
            ]);
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 10,
                'obs' => Auth::user()->name . ' removeu ' . $request->qtde . ' unidades de ' . $item->nome . ' do estoque.'
            ]);
            return redirect()->back()->with('msg', 'Removido do estoque');
        }
        return redirect()->back()->with('erroMsg', 'Erro ao remover estoque');
    }

    public function extravio(Request $request)
    {
        if (!empty($request->qtde) && !empty($request->item_id)) {
            $item = ItemModel::where('id', $request->item_id)->first();
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
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 11,
                'obs' => Auth::user()->name . ' registrou a perda de ' . $request->qtde . ' unidades de ' . $item->nome
            ]);
            return redirect()->back()->with('msg', 'Produto(os) extraviado(os).');
        }
        return redirect()->back()->with('erroMsg', 'Erro ao cadastrar.');
    }

    public function bonificacao(Request $request)
    {
        if (!empty($request->qtde) && !empty($request->item_id) && !empty($request->code)) {
            $item = ItemModel::where('id', $request->item_id)->first();
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
            HistoricoModel::create([
                'user_id' => Auth::user()->id,
                'operacao' => 11,
                'obs' => Auth::user()->name . ' registrou a bonificação de ' . $request->qtde . ' unidades de ' . $item->nome . ' para ' . $card->nome
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
                $extravio = ComandaModel::whereBetween(
                    'comanda.updated_at',
                    [date('Y-m-d', strtotime($request->dataInit)), date('Y-m-d', strtotime('+1 days', strtotime($request->dataFim)))]
                )
                    ->where('comanda.card_id', 999)
                    ->leftJoin(
                        'itens',
                        'comanda.item_id',
                        '=',
                        'itens.id'
                    )
                    ->leftJoin(
                        'estoque',
                        'comanda.item_id',
                        '=',
                        'estoque.item_id'
                    )
                    ->select('comanda.*', 'itens.nome as itemNome', 'estoque.valor', 'itens.id as itemId')->get();
                $bonificacao = ComandaModel::whereBetween(
                    'comanda.updated_at',
                    [date('Y-m-d', strtotime($request->dataInit)), date('Y-m-d', strtotime('+1 days', strtotime($request->dataFim)))]
                )
                    ->where('comanda.card_id', 888)
                    ->leftJoin(
                        'itens',
                        'comanda.item_id',
                        '=',
                        'itens.id'
                    )
                    ->leftJoin(
                        'estoque',
                        'comanda.item_id',
                        '=',
                        'estoque.item_id'
                    )
                    ->select('comanda.*', 'itens.nome as itemNome', 'estoque.valor', 'itens.id as itemId')->get();
                $permitido = $this->permissao(Auth::user()->id);
                return view('sistema.relatorio', compact('permitido', 'resumo', 'request', 'extravio', 'bonificacao'));
            } else {
                return redirect()->back()->with('erroMsg', 'A data de inicio não pode ser maior do que a final.');
            }
        }
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.relatorio', compact('permitido'));
    }

    public function indexHistorico(Request $request)
    {
        // if (Auth::user()->id == 1 || Auth::user()->id == 2) {
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.historico', compact('permitido'));
        // }
    }
}
