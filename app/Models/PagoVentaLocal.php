<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoVentaLocal extends Model
{
    protected $table = 'pagos_ventas_locales';
    protected $primaryKey = 'id_pago_venta_local';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_venta_local',
        'metodo',
        'monto',
        'referencia',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function ventaLocal()
    {
        return $this->belongsTo(VentaLocal::class, 'id_venta_local', 'id_venta_local');
    }
}
