<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsoCupon extends Model
{
    protected $table = 'usos_cupones';
    protected $primaryKey = 'id_uso_cupon';

    protected $fillable = [
        'id_cupon',
        'id_pedido',
        'id_usuario',
        'correo_invitado',
        'monto_descuento',
        'usado_en',
    ];

    protected $casts = [
        'monto_descuento' => 'decimal:2',
        'usado_en' => 'datetime',
    ];

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
