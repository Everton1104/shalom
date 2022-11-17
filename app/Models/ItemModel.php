<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemModel extends Model
{
    use HasFactory;

    protected $table = 'itens';

    protected $fillable = [
        'id',
        'nome',
        'categoria',
        'estoque_id',
        'valor',
    ];
}
