<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'contrasena',
        'id_rol',
        'activo',
        'correo_verificado_en',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'correo_verificado_en' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function ventasLocales()
    {
        return $this->hasMany(VentaLocal::class, 'id_usuario_cajero', 'id_usuario');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_usuario_realizador', 'id_usuario');
    }

    public function pagosVerificados()
    {
        return $this->hasMany(PagoPedido::class, 'id_usuario_verificador', 'id_usuario');
    }
}
