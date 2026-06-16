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
        'id_producto_variante',

        'nombre_producto',
        'sku_snapshot',

        'precio_original',
        'precio_unitario',
        'cantidad',
        'total_linea',
        'promocion_aplicada',
    ];

    protected $casts = [
        'precio_original' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'cantidad' => 'integer',
        'total_linea' => 'decimal:2',
        'promocion_aplicada' => 'boolean',
    ];

    public function ventaLocal()
    {
        return $this->belongsTo(VentaLocal::class, 'id_venta_local', 'id_venta_local');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function variante()
    {
        return $this->belongsTo(
            ProductoVariante::class,
            'id_producto_variante',
            'id_producto_variante'
        );
    }

    public function tieneVariante(): bool
    {
        return !is_null($this->id_producto_variante);
    }
}