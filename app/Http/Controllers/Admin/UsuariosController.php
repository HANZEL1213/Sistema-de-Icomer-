<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Usuario::with('rol')
            ->orderBy('nombre')
            ->get();

        return view('admin.usuarios.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        $roles = $this->obtenerRolesDisponibles();

        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email|max:190|unique:usuarios,correo',
            'telefono' => 'nullable|string|max:30',
            'password' => 'required|string|min:8|max:255',
            'id_rol' => 'required|exists:roles,id_rol',
            'activo' => 'nullable|boolean',
            'correo_verificado_en' => 'nullable|date',
        ]);

        try {
            Usuario::create([
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'password' => Hash::make($request->password),
                'id_rol' => $request->id_rol,
                'activo' => $request->has('activo') ? 1 : 0,
                'correo_verificado_en' => $request->correo_verificado_en,
            ]);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el usuario.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Usuario::with('rol')->findOrFail($id);

        return view('admin.usuarios.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = Usuario::with('rol')->findOrFail($id);

        $roles = $this->obtenerRolesDisponibles($item->id_rol);

        return view('admin.usuarios.edit', compact('item', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email|max:190|unique:usuarios,correo,' . $id . ',id_usuario',
            'telefono' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8|max:255',
            'id_rol' => 'required|exists:roles,id_rol',
            'activo' => 'nullable|boolean',
            'correo_verificado_en' => 'nullable|date',
        ]);

        try {
            $item = Usuario::findOrFail($id);

            $data = [
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'id_rol' => $request->id_rol,
                'activo' => $request->has('activo') ? 1 : 0,
                'correo_verificado_en' => $request->correo_verificado_en,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $item->update($data);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el usuario.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        try {
            $item = Usuario::findOrFail($id);
            $item->delete();

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuario eliminado correctamente.');
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No se pudo eliminar el usuario porque tiene registros relacionados.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Error al eliminar el usuario.');
        }
    }

    /* ============================================
       🔧 APOYO
    ============================================ */
    private function obtenerRolesDisponibles($idRolActual = null)
    {
        return Rol::where(function ($query) use ($idRolActual) {
                $query->where('activo', 1);

                if ($idRolActual) {
                    $query->orWhere('id_rol', $idRolActual);
                }
            })
            ->orderBy('nombre')
            ->get();
    }
}