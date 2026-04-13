<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaLocal extends Model
{
    protected $table = 'ventas_locales';
    protected $primaryKey = 'id_venta_local';

    protected $fillable = [
        'numero_ticket',
        'id_usuario_cajero',
        'nombre_cliente',
        'telefono_cliente',
        'subtotal',
        'descuento',
        'total',
        'notas',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /* ============================================
       RELACIONES
    ============================================ */

    public function cajero()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_cajero', 'id_usuario');
    }

    public function detalle()
    {
        return $this->hasMany(DetalleVentaLocal::class, 'id_venta_local', 'id_venta_local');
    }

    public function pagos()
    {
        return $this->hasMany(PagoVentaLocal::class, 'id_venta_local', 'id_venta_local');
    }

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_venta_local', 'id_venta_local');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_venta_local', 'id_venta_local');
    }


    }