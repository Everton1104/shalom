<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComandaModel;
use App\Models\ItemModel;
use App\Models\CartaoModel;

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
                ->select('comanda.*', 'itens.nome', 'itens.valor', 'itens.id as itemId')
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

    public function store(Request $request) // fazer storeUpdateFormRequest

    {

        if (isset($request->id) && isset($request->card_id)) {
            ComandaModel::create(
                [
                    'item_id' => $request->id,
                    'card_id' => $request->card_id,
                    'qtde' => $request->qtde,
                ]
            );
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
            ComandaModel::where('id', $id)->first()->delete();
        }
        return $this->index($card);
    }

    public function pagar($card)
    {
        if (isset(ComandaModel::where('card_id', $card)->first()->id)) {
            ComandaModel::where('card_id', $card)->update(['pago' => 1]);
            CartaoModel::where('id', $card)->update(['nome' => null]);
            return redirect()->back()->with('msg', 'Comanda paga.');
        }
        return redirect()->back()->with('erroMsg', 'Comanda vazia.');
    }

    public function addCard(Request $request)
    {
        if (!empty($request->code)) {
            try {
                $cartao = CartaoModel::create(['code' => $request->code]);
                return redirect()->back()->with('msg', 'Cartão ' . $cartao->id . ' criado.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('erroMsg', 'Este cartão já existe.');
            }
        }
        return redirect()->back()->with('erroMsg', 'Valor inválido');
    }

    public function indexProdutos()
    {
        $itens = ItemModel::get();
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema.produtos', compact('permitido', 'itens'));
    }

    public function novoProduto(Request $request)
    {
        if (!empty($request->nome) && !empty($request->valor)) {
            ItemModel::create([
                'nome' => $request->nome,
                'valor' => $request->valor,
            ]);
            return redirect()->back()->with('msg', 'Adicionado');
        } else {
            return redirect()->back()->with('erroMsg', 'Valores inválidos');
        }
    }

    public function deleteProduto($id) // PROCURAR TODAS AS OCORRENCIAS DDO PRODUTO ANTES DE APAGAR E COLOCAR NA COMANDA PRODUTO DELETADO DIA **/**/****  
    {
        try {
            ItemModel::where('id', $id)->delete();
            return redirect()->back()->with('msg', $id . " apagado.");
        } catch (\Throwable $th) {
            return redirect()->back()->with('erroMsg', "Erro ao apagar");
        }
    }

    public function editarProduto(Request $request)
    {
        try {
            if (!empty($request->nome) && !empty($request->valor) && !empty($request->id)) {
                ItemModel::where('id', $request->id)->update([
                    'nome' => $request->nome,
                    'valor' => $request->valor,
                ]);
                return redirect()->back()->with('msg', "Atualizaddo.");
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('erroMsg', "Erro ao apagar" . $th);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return 'edit';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return 'update';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return 'destroy';
    }
}
