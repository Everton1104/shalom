<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoqueModel extends Model
{
    use HasFactory;

    protected $table = 'estoque';

    protected $fillable = [
        'id',
        'item_id',
        'qtde',
        'valor',
        'obs'
    ];
}
