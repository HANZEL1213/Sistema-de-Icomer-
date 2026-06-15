<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProductoVariante extends Model
{
    protected $table = 'producto_variantes';
    protected $primaryKey = 'id_producto_variante';

    protected $fillable = [
        'id_producto',
        'id_opcion_variante',
        'nombre',
        'sku',

        'precio',

        'descuento_activo',
        'precio_descuento',
        'descuento_inicio',
        'descuento_fin',

        'stock_actual',
        'activo',
        'es_principal',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'descuento_activo' => 'boolean',
        'precio_descuento' => 'decimal:2',
        'descuento_inicio' => 'datetime',
        'descuento_fin' => 'datetime',
        'stock_actual' => 'integer',
        'activo' => 'boolean',
        'es_principal' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(
            Producto::class,
            'id_producto',
            'id_producto'
        );
    }

    public function opcion()
    {
        return $this->belongsTo(
            OpcionVariante::class,
            'id_opcion_variante',
            'id_opcion_variante'
        );
    }

    public function tienePromocionActiva(): bool
    {
        return $this->descuento_activo && $this->precio_descuento !== null;
    }

    public function promocionVigente(): bool
    {
        return $this->tienePromocionActiva();
    }

    public function precioVenta(): float
    {
        return $this->tienePromocionActiva()
            ? round((float) $this->precio_descuento, 2)
            : round((float) $this->precio, 2);
    }

    public function precioOriginal(): float
    {
        return round((float) $this->precio, 2);
    }

    public function stockDisponible(): int
    {
        return (int) $this->stock_actual;
    }

    public function estaDisponible(): bool
    {
        return $this->activo && $this->stock_actual > 0;
    }

    public function registrarSalidaInventario(
        $cantidad,
        string $motivo = 'Salida de inventario',
        ?int $idPedido = null,
        ?int $idVentaLocal = null,
        ?int $idUsuarioRealizador = null,
        ?string $notas = null
    ): void {
        $cantidad = (int) $cantidad;

        if ($cantidad <= 0) {
            return;
        }

        if ($cantidad > (int) $this->stock_actual) {
            throw ValidationException::withMessages([
                'cantidad' => 'La cantidad de salida no puede ser mayor al stock actual de la variante.',
            ]);
        }

        $this->decrement('stock_actual', $cantidad);
        $this->refresh();

        MovimientoInventario::create([
            'id_producto' => $this->id_producto,
            'id_producto_variante' => $this->id_producto_variante,
            'tipo' => 'salida',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'id_pedido' => $idPedido,
            'id_venta_local' => $idVentaLocal,
            'id_usuario_realizador' => $idUsuarioRealizador,
            'notas' => $notas,
        ]);
    }

    public function registrarEntradaInventario(
        $cantidad,
        string $motivo = 'Entrada de inventario',
        ?int $idPedido = null,
        ?int $idVentaLocal = null,
        ?int $idUsuarioRealizador = null,
        ?string $notas = null
    ): void {
        $cantidad = (int) $cantidad;

        if ($cantidad <= 0) {
            return;
        }

        $this->increment('stock_actual', $cantidad);
        $this->refresh();

        MovimientoInventario::create([
            'id_producto' => $this->id_producto,
            'id_producto_variante' => $this->id_producto_variante,
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'id_pedido' => $idPedido,
            'id_venta_local' => $idVentaLocal,
            'id_usuario_realizador' => $idUsuarioRealizador,
            'notas' => $notas,
        ]);
    }
}