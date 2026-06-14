<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedidos';
    protected $primaryKey = 'id_detalle_pedido';

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'id_producto_variante',
        'nombre_producto',
        'sku_snapshot',
        'precio_unitario',
        'cantidad',
        'total_linea',
        'promocion_aplicada',
        'precio_original',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'cantidad' => 'integer',
        'total_linea' => 'decimal:2',
        'promocion_aplicada' => 'boolean',
        'precio_original' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
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