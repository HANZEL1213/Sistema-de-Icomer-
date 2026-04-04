<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZonaEnvio;
use App\Models\Provincia;
use App\Models\Canton;
use App\Models\Distrito;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class ZonasEnvioController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = ZonaEnvio::with(['provincia', 'canton', 'distrito'])
            ->orderBy('id_provincia')
            ->orderBy('id_canton')
            ->orderBy('id_distrito')
            ->get();

        return view('admin.zonas_envio.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        $provincias = Provincia::orderBy('nombre')->get();

        return view('admin.zonas_envio.create', compact('provincias'));
    }

    public function store(Request $request)
    {
        $request->validate(
            $this->rules($request),
            $this->messages()
        );

        try {
            $this->validarJerarquiaUbicacion($request);

            ZonaEnvio::create($this->data($request));

            return redirect()
                ->route('admin.zonas-envio.index')
                ->with('success', 'Zona de envío creada correctamente.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la zona de envío.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = ZonaEnvio::with(['provincia', 'canton', 'distrito'])
            ->findOrFail($id);

        return view('admin.zonas_envio.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = ZonaEnvio::with(['provincia', 'canton', 'distrito'])
            ->findOrFail($id);

        $provincias = Provincia::orderBy('nombre')->get();

        $cantones = Canton::where('id_provincia', $item->id_provincia)
            ->orderBy('nombre')
            ->get();

        $distritos = Distrito::where('id_canton', $item->id_canton)
            ->orderBy('nombre')
            ->get();

        return view('admin.zonas_envio.edit', compact(
            'item',
            'provincias',
            'cantones',
            'distritos'
        ));
    }

    public function update(Request $request, string $id)
    {
        $request->validate(
            $this->rules($request, $id),
            $this->messages()
        );

        try {
            $item = ZonaEnvio::findOrFail($id);

            $this->validarJerarquiaUbicacion($request);

            $item->update($this->data($request));

            return redirect()
                ->route('admin.zonas-envio.index')
                ->with('success', 'Zona de envío actualizada correctamente.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la zona de envío.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
public function destroy(string $id)
{
    try {
        ZonaEnvio::findOrFail($id)->delete();

        return redirect()
            ->route('admin.zonas-envio.index')
            ->with('success', 'Zona de envío eliminada correctamente.');
            
    } catch (QueryException $e) {

        return redirect()
            ->route('admin.zonas-envio.index')
            ->with('error', 'No se pudo eliminar la zona de envío. Puede estar en uso.');

    } catch (\Exception $e) {

        return redirect()
            ->route('admin.zonas-envio.index')
            ->with('error', 'Error inesperado al eliminar la zona de envío.');
    }
}
    /* ============================================
       🌎 CARGA DINÁMICA
    ============================================ */
    public function obtenerCantones(string $id_provincia)
    {
        $cantones = Canton::where('id_provincia', $id_provincia)
            ->orderBy('nombre')
            ->get(['id_canton', 'nombre']);

        return response()->json($cantones);
    }

    public function obtenerDistritos(string $id_canton)
    {
        $distritos = Distrito::where('id_canton', $id_canton)
            ->orderBy('nombre')
            ->get(['id_distrito', 'nombre']);

        return response()->json($distritos);
    }

    /* ============================================
       🔒 VALIDACIONES
    ============================================ */
    private function rules(Request $request, ?string $id = null): array
    {
        $uniqueRule = Rule::unique('zonas_envio')
            ->where(function ($query) use ($request) {
                return $query->where('id_provincia', $request->id_provincia)
                    ->where('id_canton', $request->id_canton)
                    ->where('id_distrito', $request->id_distrito);
            });

        if ($id !== null) {
            $uniqueRule->ignore($id, 'id_zona_envio');
        }

        return [
            'id_provincia' => 'required|exists:provincias,id_provincia',
            'id_canton' => [
                'required',
                'exists:cantones,id_canton',
                $uniqueRule,
            ],
            'id_distrito' => 'required|exists:distritos,id_distrito',
            'costo' => 'required|numeric|min:0',
            'activo' => 'nullable',
        ];
    }

    private function messages(): array
    {
        return [
            'id_provincia.required' => 'La provincia es obligatoria.',
            'id_provincia.exists' => 'La provincia seleccionada no es válida.',
            'id_canton.required' => 'El cantón es obligatorio.',
            'id_canton.exists' => 'El cantón seleccionado no es válido.',
            'id_canton.unique' => 'Ya existe una zona de envío para esa provincia, cantón y distrito.',
            'id_distrito.required' => 'El distrito es obligatorio.',
            'id_distrito.exists' => 'El distrito seleccionado no es válido.',
            'costo.required' => 'El costo de envío es obligatorio.',
            'costo.numeric' => 'El costo de envío debe ser numérico.',
            'costo.min' => 'El costo de envío no puede ser negativo.',
        ];
    }

    private function validarJerarquiaUbicacion(Request $request): void
    {
        $canton = Canton::findOrFail($request->id_canton);
        $distrito = Distrito::findOrFail($request->id_distrito);

        if ((int) $canton->id_provincia !== (int) $request->id_provincia) {
            throw new \InvalidArgumentException(
                'El cantón seleccionado no pertenece a la provincia indicada.'
            );
        }

        if ((int) $distrito->id_canton !== (int) $request->id_canton) {
            throw new \InvalidArgumentException(
                'El distrito seleccionado no pertenece al cantón indicado.'
            );
        }
    }

    private function data(Request $request): array
    {
        return [
            'id_provincia' => $request->id_provincia,
            'id_canton' => $request->id_canton,
            'id_distrito' => $request->id_distrito,
            'costo' => $request->costo,
            'activo' => $request->has('activo') ? 1 : 0,
        ];
    }
}


