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

Route::resource('sistema', 'App\Http\Controllers\SistemaController')->middleware('auth');

Route::any('cadastro', function () {
    return 'cadastro';
})->name('cadastro');

Route::any('searchItem', 'App\Http\Controllers\SistemaController@searchItem')->middleware('auth')->name('searchItem');
Route::any('searchComanda', 'App\Http\Controllers\SistemaController@searchComanda')->middleware('auth')->name('searchComanda');
