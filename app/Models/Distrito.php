<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = 'distritos';
    protected $primaryKey = 'id_distrito';

    protected $fillable = [
        'id_canton',
        'codigo',
        'nombre',
    ];

    /* ============================================
       🔗 RELACIONES
    ============================================ */

    public function canton()
    {
        return $this->belongsTo(Canton::class, 'id_canton', 'id_canton');
    }

    public function zonasEnvio()
    {
        return $this->hasMany(ZonaEnvio::class, 'id_distrito', 'id_distrito');
    }
}