<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoPedido extends Model
{
    protected $table = 'pagos_pedidos';
    protected $primaryKey = 'id_pago_pedido';

    protected $fillable = [
        'id_pedido',
        'metodo',
        'intento',
        'es_ultimo',
        'ruta_comprobante',
        'numero_comprobante',
        'monto_reportado',
        'moneda',
        'estado',
        'enviado_en',
        'id_usuario_verificador',
        'verificado_en',
        'motivo_rechazo',
    ];

    protected $casts = [
        'intento' => 'integer',
        'es_ultimo' => 'boolean',
        'monto_reportado' => 'decimal:2',
        'enviado_en' => 'datetime',
        'verificado_en' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function verificador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_verificador', 'id_usuario');
    }
}