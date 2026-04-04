<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';
    protected $primaryKey = 'id_provincia';

    protected $fillable = [
        'codigo',
        'nombre',
    ];

    /* ============================================
       🔗 RELACIONES
    ============================================ */

    public function cantones()
    {
        return $this->hasMany(Canton::class, 'id_provincia', 'id_provincia');
    }

    public function zonasEnvio()
    {
        return $this->hasMany(ZonaEnvio::class, 'id_provincia', 'id_provincia');
    }
}