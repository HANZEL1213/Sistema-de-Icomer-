<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';

    protected $fillable = [
        'numero_pedido',
        'estado',
        'id_usuario',
        'nombre_cliente',
        'telefono_cliente',
        'correo_cliente',
        'tipo_entrega',
        'provincia_envio',
        'canton_envio',
        'distrito_envio',
        'direccion_envio',
        'referencia_envio',
        'link_google_maps',
        'costo_envio',
        'id_cupon',
        'codigo_cupon',
        'descuento',
        'subtotal',
        'subtotal_con_descuento',
        'total',
        'notas',
        'codigo_seguimiento_publico',
    ];

    protected $casts = [
        'costo_envio' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'subtotal_con_descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    public function detalle()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }

    public function pagos()
    {
        return $this->hasMany(PagoPedido::class, 'id_pedido', 'id_pedido')
            ->orderByDesc('intento');
    }

    public function pagoUltimo()
    {
        return $this->hasOne(PagoPedido::class, 'id_pedido', 'id_pedido')
            ->latestOfMany('intento');
    }

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_pedido', 'id_pedido');
    }

    public function usoCupon()
    {
        return $this->hasOne(UsoCupon::class, 'id_pedido', 'id_pedido');
    }
}