<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ItemModel;

class HomeController extends Controller
{
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

    public function index()
    {
        $alcoolicas = ItemModel::where('categoria', 1)
            ->leftJoin(
                'estoque',
                'estoque.item_id',
                '=',
                'itens.id'
            )
            ->select('itens.*', 'estoque.qtde as estoque')->orderBy('valor', 'ASC')->get();

        $porcoes = ItemModel::where('categoria', 2)
            ->leftJoin(
                'estoque',
                'estoque.item_id',
                '=',
                'itens.id'
            )
            ->select('itens.*', 'estoque.qtde as estoque')->orderBy('valor', 'ASC')->get();

        $bebidas = ItemModel::where('categoria', 3)
            ->leftJoin(
                'estoque',
                'estoque.item_id',
                '=',
                'itens.id'
            )
            ->select('itens.*', 'estoque.qtde as estoque')->orderBy('valor', 'ASC')->get();

        $doces = ItemModel::where('categoria', 4)
            ->leftJoin(
                'estoque',
                'estoque.item_id',
                '=',
                'itens.id'
            )
            ->select('itens.*', 'estoque.qtde as estoque')->orderBy('valor', 'ASC')->get();

        $meia = ItemModel::where('categoria', 5)
            ->leftJoin(
                'estoque',
                'estoque.item_id',
                '=',
                'itens.id'
            )
            ->select('itens.*', 'estoque.qtde as estoque')->orderBy('valor', 'ASC')->get();

        $salgados = ItemModel::where('categoria', 6)
            ->leftJoin(
                'estoque',
                'estoque.item_id',
                '=',
                'itens.id'
            )
            ->select('itens.*', 'estoque.qtde as estoque')->orderBy('valor', 'ASC')->get();

        $permitido = $this->permissao(Auth::user()->id ?? false);
        return view('welcome', compact('permitido', 'alcoolicas', 'porcoes', 'bebidas', 'doces', 'meia', 'salgados'));
    }
}
