<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cupon extends Model
{
    use SoftDeletes;

    protected $table = 'cupones';
    protected $primaryKey = 'id_cupon';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'minimo_subtotal',
        'inicia_en',
        'termina_en',
        'max_usos_total',
        'max_usos_por_usuario',
        'activo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'minimo_subtotal' => 'decimal:2',
        'max_usos_total' => 'integer',
        'max_usos_por_usuario' => 'integer',
        'activo' => 'boolean',
        'inicia_en' => 'datetime',
        'termina_en' => 'datetime',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cupon', 'id_cupon');
    }

    public function usos()
    {
        return $this->hasMany(UsoCupon::class, 'id_cupon', 'id_cupon');
    }
}
