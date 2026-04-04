<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CarruselItem extends Model
{
    protected $table = 'carrusel_items';
    protected $primaryKey = 'id_carrusel_item';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'ruta_imagen',
        'texto_boton',
        'tipo_destino',
        'url_destino',
        'id_producto',
        'id_categoria',
        'orden',
        'orden_programado',
        'activo_manual',
        'activo',
        'inicia_en',
        'termina_en',
    ];

    protected $casts = [
        'activo_manual' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer',
        'orden_programado' => 'integer',
        'inicia_en' => 'datetime',
        'termina_en' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', 1);
    }

    public function scopeVigentes(Builder $query): Builder
    {
        $ahora = now();

        return $query
            ->where('inicia_en', '<=', $ahora)
            ->where('termina_en', '>', $ahora);
    }

    public function scopeEnCarrusel(Builder $query): Builder
    {
        return $query
            ->where('activo', 1)
            ->where('orden', '>', 0)
            ->orderBy('orden')
            ->orderBy('id_carrusel_item');
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query
            ->orderBy('orden')
            ->orderBy('id_carrusel_item');
    }

    public function yaInicio(): bool
    {
        return $this->inicia_en !== null && $this->inicia_en->lte(now());
    }

    public function estaVencido(): bool
    {
        return $this->termina_en !== null && $this->termina_en->lte(now());
    }

    public function estaVigente(): bool
    {
        $ahora = now();

        return $this->inicia_en !== null
            && $this->termina_en !== null
            && $this->inicia_en->lte($ahora)
            && $this->termina_en->gt($ahora);
    }

    public function debeEstarActivoAhora(): bool
    {
        return (bool) $this->activo_manual && $this->estaVigente();
    }

    public function estaDisponibleParaCarrusel(): bool
    {
        return (bool) $this->activo
            && $this->estaVigente()
            && (int) $this->orden > 0;
    }
}