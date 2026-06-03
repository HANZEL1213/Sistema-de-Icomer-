<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TOGGLE FAVORITO
    |--------------------------------------------------------------------------
    */

    public function toggle(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | USUARIO LOGUEADO
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {

            $favorito = Favorito::where('id_usuario', Auth::user()->id_usuario)
                ->where('id_producto', $producto->id_producto)
                ->first();

            if ($favorito) {

                $favorito->delete();

                return response()->json([
                    'success' => true,
                    'favorito' => false,
                    'cantidadFavoritos' => $this->cantidadFavoritos($request),
                    'message' => 'Producto eliminado de favoritos.',
                ]);
            }

            Favorito::create([
                'id_usuario'  => Auth::user()->id_usuario,
                'id_producto' => $producto->id_producto,
            ]);

            return response()->json([
                'success' => true,
                'favorito' => true,
                'cantidadFavoritos' => $this->cantidadFavoritos($request),
                'message' => 'Producto agregado a favoritos.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VISITANTE POR SESSION
        |--------------------------------------------------------------------------
        */

        $sessionId = $request->session()->getId();

        $favorito = Favorito::where('session_id', $sessionId)
            ->where('id_producto', $producto->id_producto)
            ->first();

        if ($favorito) {

            $favorito->delete();

            return response()->json([
                'success' => true,
                'favorito' => false,
                'cantidadFavoritos' => $this->cantidadFavoritos($request),
                'message' => 'Producto eliminado de favoritos.',
            ]);
        }

        Favorito::create([
            'session_id'  => $sessionId,
            'id_producto' => $producto->id_producto,
        ]);

        return response()->json([
            'success' => true,
            'favorito' => true,
            'cantidadFavoritos' => $this->cantidadFavoritos($request),
            'message' => 'Producto agregado a favoritos.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTA FAVORITOS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        if (Auth::check()) {

            $favoritos = Favorito::with('producto.imagenPrincipal')
                ->where('id_usuario', Auth::user()->id_usuario)
                ->latest()
                ->get();

        } else {

            $favoritos = Favorito::with('producto.imagenPrincipal')
                ->where('session_id', session()->getId())
                ->latest()
                ->get();

        }

        return view('tienda.favoritos.index', compact('favoritos'));
    }

    /*
    |--------------------------------------------------------------------------
    | CANTIDAD FAVORITOS
    |--------------------------------------------------------------------------
    */

    private function cantidadFavoritos(Request $request)
    {
        return Favorito::where(function ($query) use ($request) {

            if (Auth::check()) {

                $query->where('id_usuario', Auth::user()->id_usuario);

            } else {

                $query->where('session_id', $request->session()->getId());

            }

        })->count();
    }
}