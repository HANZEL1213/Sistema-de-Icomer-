<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteMasivo extends Model
{
    protected $table = 'lotes_masivos';
    protected $primaryKey = 'id_lote_masivo';

    // La tabla solo tiene created_at (sin updated_at), así que se
    // desactiva el manejo automático de timestamps de Eloquent.
    public $timestamps = false;

    protected $fillable = [
        'operacion',
        'id_categoria',
        'id_marca',
        'id_usuario',
        'total_solicitado',
        'total_ok',
        'total_error',
        'detalle',
    ];

    protected $casts = [
        'total_solicitado' => 'integer',
        'total_ok' => 'integer',
        'total_error' => 'integer',
        'detalle' => 'array', // el JSON de errores por archivo/producto se
                              // castea a array automáticamente al leer/escribir
        'created_at' => 'datetime',
    ];

    // Se fija created_at al crear, ya que $timestamps = false lo apaga
    // por completo (incluido el valor por defecto que pondría Eloquent).
    protected static function booted()
    {
        static::creating(function (LoteMasivo $lote) {
            $lote->created_at ??= now();
        });
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id_marca');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}