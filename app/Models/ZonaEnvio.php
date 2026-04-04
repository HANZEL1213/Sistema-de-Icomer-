<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaEnvio extends Model
{
    protected $table = 'zonas_envio';
    protected $primaryKey = 'id_zona_envio';

    protected $fillable = [
        'id_provincia',
        'id_canton',
        'id_distrito',
        'costo',
        'activo',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /* ============================================
       🔗 RELACIONES
    ============================================ */

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia', 'id_provincia');
    }

    public function canton()
    {
        return $this->belongsTo(Canton::class, 'id_canton', 'id_canton');
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'id_distrito', 'id_distrito');
    }
}