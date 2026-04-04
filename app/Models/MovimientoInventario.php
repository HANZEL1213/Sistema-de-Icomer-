<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'id_movimiento_inventario';

    protected $fillable = [
        'id_producto',
        'tipo',
        'cantidad',
        'motivo',
        'id_pedido',
        'id_venta_local',
        'id_usuario_realizador',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function ventaLocal()
    {
        return $this->belongsTo(VentaLocal::class, 'id_venta_local', 'id_venta_local');
    }

    public function realizador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_realizador', 'id_usuario');
    }
}
