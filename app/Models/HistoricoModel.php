<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoModel extends Model
{
    use HasFactory;

    protected $table = 'historico';

    protected $fillable = [
        'obs',
        'user_id',
        'operacao'
    ];
}
