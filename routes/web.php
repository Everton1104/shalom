<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

function permissao($id)
{
    switch ($id) {
        case '1':
            return true;
        case '2':
            return true;
        default:
            return false;
    }
}

Auth::routes();

Route::get('/', function () {
    $permitido = isset(Auth::user()->id) ? permissao(Auth::user()->id) : false;
    return view('welcome', compact('permitido'));
});

Route::any('altPass', 'App\Http\Controllers\SistemaController@alterarSenha')->name('altPass')->middleware('auth');

Route::any('sistema/estoque/remove', 'App\Http\Controllers\SistemaController@removeEstoque')->name('sistema.removeEstoque')->middleware('auth');
Route::any('sistema/estoque/add', 'App\Http\Controllers\SistemaController@addEstoque')->name('sistema.addEstoque')->middleware('auth');
Route::any('sistema/estoque', 'App\Http\Controllers\SistemaController@indexEstoque')->name('sistema.estoque')->middleware('auth');
Route::any('sistema/relatorio', 'App\Http\Controllers\SistemaController@indexRelatorio')->name('sistema.relatorio')->middleware('auth');
Route::any('sistema/searchNomeAberto', 'App\Http\Controllers\SistemaController@searchNomeAberto')->name('sistema.searchNomeAberto')->middleware('auth');
Route::any('sistema/indexAberto', 'App\Http\Controllers\SistemaController@indexAberto')->name('sistema.indexAberto')->middleware('auth');
Route::any('sistema/aberto', 'App\Http\Controllers\SistemaController@aberto')->name('sistema.aberto')->middleware('auth');
Route::any('sistema/pagar', 'App\Http\Controllers\SistemaController@pagar')->name('sistema.pagar')->middleware('auth');
Route::any('sistema/addCard', 'App\Http\Controllers\SistemaController@addCard')->name('sistema.addCard')->middleware('auth');
Route::any('sistema/cadastroProdutos', 'App\Http\Controllers\SistemaController@indexProdutos')->name('sistema.cadastroProdutos')->middleware('auth');
Route::any('sistema/cadastroProdutos/novo', 'App\Http\Controllers\SistemaController@novoProduto')->name('sistema.novoProduto')->middleware('auth');
Route::any('sistema/cadastroProdutos/delete/{id}', 'App\Http\Controllers\SistemaController@deleteProduto')->name('sistema.deleteProduto')->middleware('auth');
Route::any('sistema/cadastroProdutos/edit', 'App\Http\Controllers\SistemaController@editarProduto')->name('sistema.editarProduto')->middleware('auth');
Route::any('sistema/cadastroProdutos/search', 'App\Http\Controllers\SistemaController@searchProduto')->name('sistema.searchProduto')->middleware('auth');
Route::any('sistema/searchNome', 'App\Http\Controllers\SistemaController@searchNome')->name('sistema.searchNome')->middleware('auth');
Route::any('sistema/{card}/{id}', 'App\Http\Controllers\SistemaController@deletar')->name('sistema.delete')->middleware('auth');
Route::resource('sistema', 'App\Http\Controllers\SistemaController')->middleware('auth');

Route::any('searchItem', 'App\Http\Controllers\SistemaController@searchItem')->middleware('auth')->name('searchItem');
Route::any('searchComanda', 'App\Http\Controllers\SistemaController@searchComanda')->middleware('auth')->name('searchComanda');
