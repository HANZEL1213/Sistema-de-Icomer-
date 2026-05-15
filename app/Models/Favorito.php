<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $table = 'favoritos';
    protected $primaryKey = 'id_favorito';

    protected $fillable = [
        'id_usuario',
        'session_id',
        'id_producto',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}