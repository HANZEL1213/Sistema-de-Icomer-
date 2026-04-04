<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarruselItem;
use App\Models\Producto;
use App\Models\Categoria;
use App\Services\CarruselSyncService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CarruselItemsController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = CarruselItem::with(['producto', 'categoria'])
            ->orderByRaw('CASE WHEN activo = 1 AND orden > 0 THEN 0 ELSE 1 END')
            ->orderByRaw('CASE WHEN activo = 1 AND orden > 0 THEN orden ELSE orden_programado END')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.carrusel.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        $productos = Producto::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('admin.carrusel.create', compact('productos', 'categorias'));
    }

    public function store(Request $request, CarruselSyncService $syncService)
    {
        $data = $this->validarItem($request, true);

        DB::beginTransaction();

        try {
            $rutaImagen = $request->file('ruta_imagen')->store('carrusel', 'public');

            CarruselItem::create([
                'titulo' => $data['titulo'] ?? null,
                'subtitulo' => $data['subtitulo'] ?? null,
                'ruta_imagen' => $rutaImagen,
                'texto_boton' => $data['texto_boton'] ?? null,

                'tipo_destino' => $data['tipo_destino'] ?? null,
                'url_destino' => $this->resolverUrlDestino($data),
                'id_producto' => $this->resolverProductoDestino($data),
                'id_categoria' => $this->resolverCategoriaDestino($data),

                'orden_programado' => max(1, (int) $data['orden_programado']),
                'activo_manual' => $request->boolean('activo_manual') ? 1 : 0,

                // Estos campos los controla el service
                'activo' => 0,
                'orden' => 0,

                'inicia_en' => Carbon::parse($data['inicia_en']),
                'termina_en' => Carbon::parse($data['termina_en']),
            ]);

            $syncService->sincronizar(now());

            DB::commit();

            return redirect()
                ->route('admin.carrusel-items.index')
                ->with('success', 'Elemento del carrusel creado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el elemento del carrusel.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = CarruselItem::with(['producto', 'categoria'])->findOrFail($id);

        return view('admin.carrusel.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = CarruselItem::findOrFail($id);

        $productos = Producto::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('admin.carrusel.edit', compact('item', 'productos', 'categorias'));
    }

    public function update(Request $request, string $id, CarruselSyncService $syncService)
    {
        $item = CarruselItem::findOrFail($id);
        $data = $this->validarItem($request, false);

        if ($request->input('eliminar_imagen') === '1' && !$request->hasFile('ruta_imagen')) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'ruta_imagen' => 'Debes seleccionar una nueva imagen para reemplazar la actual.',
                ]);
        }

        DB::beginTransaction();

        try {
            $rutaImagenAnterior = $item->ruta_imagen;
            $rutaImagenFinal = $rutaImagenAnterior;
            $debeEliminarImagenAnterior = false;

            if ($request->hasFile('ruta_imagen')) {
                $rutaImagenFinal = $request->file('ruta_imagen')->store('carrusel', 'public');
                $debeEliminarImagenAnterior = $rutaImagenAnterior
                    && Storage::disk('public')->exists($rutaImagenAnterior);
            }

            $item->update([
                'titulo' => $data['titulo'] ?? null,
                'subtitulo' => $data['subtitulo'] ?? null,
                'ruta_imagen' => $rutaImagenFinal,
                'texto_boton' => $data['texto_boton'] ?? null,

                'tipo_destino' => $data['tipo_destino'] ?? null,
                'url_destino' => $this->resolverUrlDestino($data),
                'id_producto' => $this->resolverProductoDestino($data),
                'id_categoria' => $this->resolverCategoriaDestino($data),

                'orden_programado' => max(1, (int) $data['orden_programado']),
                'activo_manual' => $request->boolean('activo_manual') ? 1 : 0,

                // activo y orden siguen siendo controlados por el service
                'inicia_en' => Carbon::parse($data['inicia_en']),
                'termina_en' => Carbon::parse($data['termina_en']),
            ]);

            $syncService->sincronizar(now());

            if ($debeEliminarImagenAnterior && $rutaImagenAnterior !== $rutaImagenFinal) {
                Storage::disk('public')->delete($rutaImagenAnterior);
            }

            DB::commit();

            return redirect()
                ->route('admin.carrusel-items.index')
                ->with('success', 'Elemento del carrusel actualizado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el elemento del carrusel.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id, CarruselSyncService $syncService)
    {
        DB::beginTransaction();

        try {
            $item = CarruselItem::findOrFail($id);
            $rutaImagen = $item->ruta_imagen;

            $item->delete();

            $syncService->sincronizar(now());

            if ($rutaImagen && Storage::disk('public')->exists($rutaImagen)) {
                Storage::disk('public')->delete($rutaImagen);
            }

            DB::commit();

            return redirect()
                ->route('admin.carrusel-items.index')
                ->with('success', 'Elemento eliminado correctamente.');
        } catch (QueryException $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->route('admin.carrusel-items.index')
                ->with('error', 'No se pudo eliminar el elemento.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->route('admin.carrusel-items.index')
                ->with('error', 'Error al eliminar el elemento.');
        }
    }

    /* ============================================
       ✅ VALIDACIÓN
    ============================================ */
    private function validarItem(Request $request, bool $imagenRequerida = true): array
    {
        return $request->validate([
            'titulo' => 'nullable|string|max:120',
            'subtitulo' => 'nullable|string|max:180',
            'ruta_imagen' => ($imagenRequerida ? 'required' : 'nullable') . '|image|mimes:jpg,jpeg,png,webp|max:2048',
            'texto_boton' => 'nullable|string|max:50',

            'tipo_destino' => 'nullable|in:url,producto,categoria',
            'url_destino' => 'nullable|required_if:tipo_destino,url|prohibited_unless:tipo_destino,url|url|max:255',
            'id_producto' => 'nullable|required_if:tipo_destino,producto|prohibited_unless:tipo_destino,producto|exists:productos,id_producto',
            'id_categoria' => 'nullable|required_if:tipo_destino,categoria|prohibited_unless:tipo_destino,categoria|exists:categorias,id_categoria',

            'orden_programado' => 'required|integer|min:1',
            'inicia_en' => 'required|date',
            'termina_en' => 'required|date|after:inicia_en',

            'activo_manual' => 'nullable|boolean',
            'eliminar_imagen' => 'nullable|in:0,1',
        ], [
            'ruta_imagen.required' => 'La imagen es obligatoria.',
            'orden_programado.required' => 'La posición programada es obligatoria.',
            'orden_programado.min' => 'La posición mínima permitida es 1.',
            'inicia_en.required' => 'La fecha de inicio es obligatoria.',
            'termina_en.required' => 'La fecha de finalización es obligatoria.',
            'termina_en.after' => 'La fecha de finalización debe ser mayor que la fecha de inicio.',

            'url_destino.required_if' => 'La URL es obligatoria cuando el destino es URL.',
            'url_destino.prohibited_unless' => 'La URL solo puede enviarse cuando el destino seleccionado es URL.',
            'url_destino.url' => 'La URL debe tener un formato válido.',

            'id_producto.required_if' => 'El producto es obligatorio cuando el destino es producto.',
            'id_producto.prohibited_unless' => 'El producto solo puede enviarse cuando el destino seleccionado es producto.',

            'id_categoria.required_if' => 'La categoría es obligatoria cuando el destino es categoría.',
            'id_categoria.prohibited_unless' => 'La categoría solo puede enviarse cuando el destino seleccionado es categoría.',
        ]);
    }

    private function resolverUrlDestino(array $data): ?string
    {
        return ($data['tipo_destino'] ?? null) === 'url'
            ? $data['url_destino']
            : null;
    }

    private function resolverProductoDestino(array $data): ?int
    {
        return ($data['tipo_destino'] ?? null) === 'producto'
            ? (int) $data['id_producto']
            : null;
    }

    private function resolverCategoriaDestino(array $data): ?int
    {
        return ($data['tipo_destino'] ?? null) === 'categoria'
            ? (int) $data['id_categoria']
            : null;
    }
}