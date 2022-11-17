<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComandaModel extends Model
{
    use HasFactory;

    protected $table = 'comanda';

    protected $fillable = [
        'card_id',
        'item_id',
        'qtde',
        'tipo',
        'nome',
        'obs',
        'pago'
    ];
}
