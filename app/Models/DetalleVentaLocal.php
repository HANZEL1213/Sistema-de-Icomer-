<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaLocal extends Model
{
    protected $table = 'detalle_ventas_locales';
    protected $primaryKey = 'id_detalle_venta_local';

    protected $fillable = [
        'id_venta_local',
        'id_producto',
        'nombre_producto',
        'sku_snapshot',
        'precio_unitario',
        'cantidad',
        'total_linea',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'cantidad' => 'integer',
        'total_linea' => 'decimal:2',
    ];

    public function ventaLocal()
    {
        return $this->belongsTo(VentaLocal::class, 'id_venta_local', 'id_venta_local');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
