<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'id_marca',
        'nombre',
        'slug',
        'codigo',
        'sku',
        'descripcion',
        'precio',
        'descuento_activo',
        'precio_descuento',
        'descuento_inicio',
        'descuento_fin',
        'stock_actual',
        'usa_variantes',
        'id_tipo_variante',
        'activo',
        'destacado',
        'id_categoria_principal',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'descuento_activo' => 'boolean',
        'precio_descuento' => 'decimal:2',
        'descuento_inicio' => 'datetime',
        'descuento_fin' => 'datetime',
        'stock_actual' => 'integer',
        'usa_variantes' => 'boolean',
        'activo' => 'boolean',
        'destacado' => 'boolean',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id_marca');
    }

    public function categoriaPrincipal()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_principal', 'id_categoria');
    }

    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,
            'categorias_productos',
            'id_producto',
            'id_categoria'
        )->withPivot(['id_categoria_producto', 'created_at']);
    }

    public function tipoVariante()
    {
        return $this->belongsTo(
            TipoVariante::class,
            'id_tipo_variante',
            'id_tipo_variante'
        );
    }

    public function variantes()
    {
        return $this->hasMany(
            ProductoVariante::class,
            'id_producto',
            'id_producto'
        );
    }

    public function variantesActivas()
    {
        return $this->hasMany(
            ProductoVariante::class,
            'id_producto',
            'id_producto'
        )
        ->where('activo', 1)
        ->orderByDesc('es_principal')
        ->orderBy('id_producto_variante');
    }

    public function variantePrincipal()
    {
        return $this->hasOne(
            ProductoVariante::class,
            'id_producto',
            'id_producto'
        )
        ->where('activo', 1)
        ->where('es_principal', 1);
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'id_producto', 'id_producto')
            ->orderBy('orden')
            ->orderBy('id_imagen_producto');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenProducto::class, 'id_producto', 'id_producto')
            ->where('es_principal', 1);
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'id_producto', 'id_producto');
    }

    public function detalleVentasLocales()
    {
        return $this->hasMany(DetalleVentaLocal::class, 'id_producto', 'id_producto');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_producto', 'id_producto');
    }

    public function relacionados()
    {
        return $this->belongsToMany(
            Producto::class,
            'productos_relacionados',
            'id_producto',
            'id_producto_relacionado'
        );
    }

    public function relacionadoDe()
    {
        return $this->belongsToMany(
            Producto::class,
            'productos_relacionados',
            'id_producto_relacionado',
            'id_producto'
        );
    }

    public function registrarSalidaInventario($cantidad, $motivo, $idPedido = null, $idVentaLocal = null, $idUsuario = 1, $notas = null)
    {
        if ($this->usa_variantes) {
            throw new \LogicException('Este producto usa variantes. Debes descontar inventario desde la variante seleccionada.');
        }

        $this->decrement('stock_actual', $cantidad);

        return MovimientoInventario::create([
            'id_producto' => $this->id_producto,
            'tipo' => 'salida',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'id_pedido' => $idPedido,
            'id_venta_local' => $idVentaLocal,
            'id_usuario_realizador' => $idUsuario,
            'notas' => $notas,
        ]);
    }

    public function registrarEntradaInventario($cantidad, $motivo, $idPedido = null, $idVentaLocal = null, $idUsuario = 1, $notas = null)
    {
        if ($this->usa_variantes) {
            throw new \LogicException('Este producto usa variantes. Debes aumentar inventario desde la variante seleccionada.');
        }

        $this->increment('stock_actual', $cantidad);

        return MovimientoInventario::create([
            'id_producto' => $this->id_producto,
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'id_pedido' => $idPedido,
            'id_venta_local' => $idVentaLocal,
            'id_usuario_realizador' => $idUsuario,
            'notas' => $notas,
        ]);
    }

public function tienePromocionActiva(): bool
{
    if ($this->usa_variantes) {
        return false;
    }

    return $this->descuento_activo && $this->precio_descuento !== null;
}

public function precioVenta(): float
{
    if ($this->usa_variantes) {
        $variante = $this->obtenerVarianteInicial();

        return round((float) ($variante?->precio ?? 0), 2);
    }

    return $this->tienePromocionActiva()
        ? round((float) $this->precio_descuento, 2)
        : round((float) $this->precio, 2);
}

public function stockDisponible(): int
{
    if ($this->usa_variantes) {
        return (int) $this->variantesActivas()->sum('stock_actual');
    }

    return (int) $this->stock_actual;
}

public function esVendibleComoProductoSimple(): bool
{
    return !$this->usa_variantes;
}

public function requiereSeleccionVariante(): bool
{
    return $this->usa_variantes;
}

public function tieneVariantesActivas(): bool
{
    return $this->usa_variantes && $this->variantesActivas()->exists();
}

public function obtenerVarianteInicial()
{
    if (!$this->usa_variantes) {
        return null;
    }

    $principal = $this->relationLoaded('variantePrincipal')
        ? $this->variantePrincipal
        : $this->variantePrincipal()->first();

    if ($principal) {
        return $principal;
    }

    return $this->variantesActivas()->first();
}
}