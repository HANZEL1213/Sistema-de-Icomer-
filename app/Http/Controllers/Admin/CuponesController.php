<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CuponesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Cupon::orderByDesc('created_at')->get();

        return view('admin.cupones.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        return view('admin.cupones.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);

        try {
            Cupon::create($data);

            return redirect()
                ->route('admin.cupones.index')
                ->with('success', 'Cupón creado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el cupón.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Cupon::findOrFail($id);

        return view('admin.cupones.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = Cupon::findOrFail($id);

        return view('admin.cupones.edit', compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $item = Cupon::findOrFail($id);
        $data = $this->validarDatos($request, $item->id_cupon);

        try {
            $item->update($data);

            return redirect()
                ->route('admin.cupones.index')
                ->with('success', 'Cupón actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cupón.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        try {
            $item = Cupon::findOrFail($id);
            $item->delete();

            return redirect()
                ->route('admin.cupones.index')
                ->with('success', 'Cupón eliminado correctamente.');
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.cupones.index')
                ->with('error', 'No se pudo eliminar el cupón.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.cupones.index')
                ->with('error', 'Error al eliminar el cupón.');
        }
    }

    /* ============================================
       🔒 VALIDACIÓN Y NORMALIZACIÓN
    ============================================ */
    private function validarDatos(Request $request, ?int $idCupon = null): array
    {
        $codigoNormalizado = strtoupper(trim((string) $request->input('codigo')));

        $data = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:60',
                Rule::unique('cupones', 'codigo')
                    ->ignore($idCupon, 'id_cupon')
                    ->whereNull('deleted_at'),
            ],
            'tipo' => [
                'required',
                Rule::in(['porcentaje', 'monto_fijo']),
            ],
            'valor' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'minimo_subtotal' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'inicia_en' => [
                'nullable',
                'date',
            ],
            'termina_en' => [
                'nullable',
                'date',
                'after_or_equal:inicia_en',
            ],
            'max_usos_total' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'max_usos_por_usuario' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
        ]);

        if ($codigoNormalizado === '') {
            throw ValidationException::withMessages([
                'codigo' => 'El código del cupón es obligatorio.',
            ]);
        }

        if ($data['tipo'] === 'porcentaje' && (float) $data['valor'] > 100) {
            throw ValidationException::withMessages([
                'valor' => 'Cuando el cupón es de tipo porcentaje, el valor no puede ser mayor a 100.',
            ]);
        }

        $iniciaEn = $data['inicia_en'] ?? null;
        $terminaEn = $data['termina_en'] ?? null;

        if ($iniciaEn && $terminaEn && $terminaEn < $iniciaEn) {
            throw ValidationException::withMessages([
                'termina_en' => 'La fecha de finalización no puede ser menor que la fecha de inicio.',
            ]);
        }

        return [
            'codigo' => $codigoNormalizado,
            'tipo' => $data['tipo'],
            'valor' => $data['valor'],
            'minimo_subtotal' => $data['minimo_subtotal'] ?? 0,
            'inicia_en' => $iniciaEn,
            'termina_en' => $terminaEn,
            'max_usos_total' => $data['max_usos_total'] ?? null,
            'max_usos_por_usuario' => $data['max_usos_por_usuario'] ?? null,
            'activo' => $request->has('activo') ? 1 : 0,
        ];
    }
}