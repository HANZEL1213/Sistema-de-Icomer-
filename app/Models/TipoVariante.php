<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVariante extends Model
{
    protected $table = 'tipos_variantes';

    protected $primaryKey = 'id_tipo_variante';

    protected $fillable = [
        'nombre',
        'etiqueta',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function opciones()
    {
        return $this->hasMany(
            OpcionVariante::class,
            'id_tipo_variante',
            'id_tipo_variante'
        )->orderBy('orden');
    }

    public function opcionesActivas()
    {
        return $this->hasMany(
            OpcionVariante::class,
            'id_tipo_variante',
            'id_tipo_variante'
        )
        ->where('activo', 1)
        ->orderBy('orden');
    }

    public function productos()
    {
        return $this->hasMany(
            Producto::class,
            'id_tipo_variante',
            'id_tipo_variante'
        );
    }
}