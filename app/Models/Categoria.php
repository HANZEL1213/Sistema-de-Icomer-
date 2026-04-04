<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productosComoPrincipal()
    {
        return $this->hasMany(Producto::class, 'id_categoria_principal', 'id_categoria');
    }

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'categorias_productos',
            'id_categoria',
            'id_producto'
        )->withPivot(['id_categoria_producto', 'created_at']);
    }

    public function carruselItems()
    {
        return $this->hasMany(CarruselItem::class, 'id_categoria', 'id_categoria');
    }
}