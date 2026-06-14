<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpcionVariante extends Model
{
    protected $table = 'opciones_variantes';

    protected $primaryKey = 'id_opcion_variante';

    protected $fillable = [
        'id_tipo_variante',
        'valor',
        'etiqueta',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipo()
    {
        return $this->belongsTo(
            TipoVariante::class,
            'id_tipo_variante',
            'id_tipo_variante'
        );
    }

    public function variantesProducto()
    {
        return $this->hasMany(
            ProductoVariante::class,
            'id_opcion_variante',
            'id_opcion_variante'
        );
    }
}