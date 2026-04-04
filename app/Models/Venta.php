<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';

    protected $fillable = [
        'canal',
        'id_pedido',
        'id_venta_local',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function ventaLocal()
    {
        return $this->belongsTo(VentaLocal::class, 'id_venta_local', 'id_venta_local');
    }
}
