<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UsoCupon;

class UsosCuponesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = UsoCupon::with(['cupon', 'pedido', 'usuario'])
            ->orderByDesc('usado_en')
            ->get();

        return view('admin.cupones.usos_cupones.index', compact('items'));
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = UsoCupon::with(['cupon', 'pedido', 'usuario'])
            ->findOrFail($id);

        return view('admin.cupones.usos_cupones.show', compact('item'));
    }
}