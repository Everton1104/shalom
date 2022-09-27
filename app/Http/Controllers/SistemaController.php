<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComandaModel;
use App\Models\ItemModel;

class SistemaController extends Controller
{
    public function permissao($id){
        switch ($id) {
            case '1':
                return true;
            case '2':
                return true;
            default:
                return false;
        }
    }

    public function search(Request $request){
        $lista = array();
        foreach (ItemModel::get() as $item) {
            array_push($lista, $item->nome);
        }
        return json_encode($lista);
    }

    public function index()
    {
        $permitido = $this->permissao(Auth::user()->id);
        return view('sistema', compact('permitido'));
    }

    public function create()
    {
        return 'create';
    }

    public function store(Request $request)
    {
        if(isset(ComandaModel::find($request->id)->id)){
            $comanda = ComandaModel::find($request->id);
            $permitido = $this->permissao(Auth::user()->id);
            return view('sistema', compact('comanda', 'permitido'));
        }else{
            return redirect()->back()->with('msg','Não encontrado');
        }
    }

    public function show($id)
    {
        return "show";
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
