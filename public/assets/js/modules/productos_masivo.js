/**
 * productos_masivo.js — CRUD masivo por Categoría + Marca
 *
 * FASE 2: `Fuente` habla con el servidor de verdad (fetch a las rutas de
 * data-url-*). El resto del archivo (secciones 3 a 7) no cambió respecto
 * a la Fase 1.
 *
 * FASE 2.1 — FIX 419 (CSRF / sesión):
 * Antes, el token CSRF se leía UNA sola vez del <meta name="csrf-token">
 * al cargar la página, y ese valor quedaba fijo para todas las peticiones
 * siguientes. Laravel, en cambio, renueva la cookie `XSRF-TOKEN` en CADA
 * respuesta mientras la sesión siga viva. Ahora leemos esa cookie fresca
 * en cada request (con fallback al <meta> si por algún motivo no existe),
 * así que un token "viejo" ya no genera un 419 falso.
 *
 * Si a pesar de esto el 419 sigue apareciendo de inmediato (no después de
 * un rato), ya no es un tema de token viejo: significa que la cookie de
 * sesión ni siquiera le está llegando al navegador (dominio distinto al
 * de APP_URL/SESSION_DOMAIN, o SESSION_SECURE_COOKIE=true en un sitio
 * que corre por HTTP). Eso se revisa en el .env, no en este archivo.
 *
 * Secciones:
 *   1. Utilidades
 *   2. Fuente de datos (conectada al backend)
 *   3. Estado y referencias al DOM
 *   4. Render (contexto, filas, paginación, selección)
 *   5. Carga (consulta a Fuente)
 *   6. Eventos
 *   7. Modales y resultado (Eliminar, Editar y Cargar imágenes)
 */
(function () {
    'use strict';

    const app = document.getElementById('masivo-app');
    if (!app) return;

    /* ───────────────────────── 1. Utilidades ───────────────────────── */

    const $ = (id) => document.getElementById(id);

    const esc = (s) =>
        String(s ?? '').replace(/[&<>"']/g, (c) =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const pad4 = (n) => String(n).padStart(4, '0');

    // Mismo formato que el panel: ₡32.000
    const colones = (n) =>
        '₡' + Math.round(Number(n)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    const esperar = (ms) => new Promise((r) => setTimeout(r, ms));

    function debounce(fn, ms) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), ms);
        };
    }

    // Plantilla de URL tipo ".../productos/:id" → ".../productos/1004"
    const urlConId = (plantilla, id) => (plantilla ? plantilla.replace(':id', id) : '#');

    // Lee una cookie por nombre (ya decodificada). Devuelve null si no existe.
    function leerCookie(nombre) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + nombre + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    // ¿El error viene de una sesión/CSRF vencida? (mismo texto que lanza post())
    const esErrorSesion = (err) => /sesión expiró/i.test(err?.message || '');

    // Acción reutilizable para los avisos de resultado: botón "Recargar página".
    const accionRecargar = () => ({ texto: 'Recargar página', onClick: () => window.location.reload() });

    /* ───────────────────── 2. Fuente de datos (conectada al backend) ─────────────────────
        * Contrato de cada método — no cambiar las claves sin actualizar el controlador:
     *
     *   consultar({ categoria, marca, q, page, perPage })
     *     → { items[], total, idsFiltrados[], idsConVariantes[], totalPar, proximoLote, pagina }
     *   cambiarActivo(id, activo) → true|false
     *   crearTanda({ categoria, marca, token, precio, stock, archivos })
     *     → { ok, primerLote, ultimoLote, errores[] }
     *   previsualizarBorrado(ids) → { total, muestra[], enCarrusel, conVariantes }
     *   editar(ids, cambios) → { solicitados, ok, idsEditados[], errores[], avisos[] }
     *   eliminar(ids) → { solicitados, ok, idsEliminados[], errores[] }
     */
    const Fuente = (() => {
        const tandasProcesadas = new Map(); // token → resultado (idempotencia también en el cliente)
        const PALETA = ['#111111', '#7d8b6a', '#a83232', '#5a6a8a', '#8a6a12', '#6b4c8a', '#c9a227', '#2b2b2b'];

        // Cabecera CSRF a mandar en cada POST. Preferimos la cookie XSRF-TOKEN
        // porque Laravel la renueva en cada respuesta (nunca queda vieja aunque
        // la pestaña lleve rato abierta). Si no existe, caemos al <meta> que
        // trajo la página al cargar.
        function headersCsrf() {
            const cookie = leerCookie('XSRF-TOKEN');
            if (cookie) return { 'X-XSRF-TOKEN': cookie };

            const meta = document.querySelector('meta[name="csrf-token"]')?.content;
            return meta ? { 'X-CSRF-TOKEN': meta } : {};
        }

        async function leerError(r) {
            try {
                const j = await r.json();
                return j.message ?? `HTTP ${r.status}`;
            } catch {
                return `HTTP ${r.status}`;
            }
        }

        // POST con body ya armado (FormData para archivos, o texto).
        async function post(url, body, extraHeaders = {}) {
            if (!url || url === 'undefined') {
                throw new Error('Falta configurar la URL de esta acción en el HTML (revisá el atributo data-url-* correspondiente).');
            }

            const r = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin', // asegura que la cookie de sesión viaje siempre
               headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', ...headersCsrf(), ...extraHeaders },
                body,
            });

            // 419 = token CSRF vencido o sesión expirada. Mensaje claro en
            // vez del genérico "HTTP 419", que confunde al usuario.
            if (r.status === 419) {
                throw new Error('Tu sesión expiró. Recargá la página (F5) e intentá de nuevo.');
            }

            if (!r.ok) throw new Error(await leerError(r));
            return r.json();
        }

        // POST con body JSON (el caso común: editar, eliminar, previsualizar).
        const postJson = (url, datos) =>
            post(url, JSON.stringify(datos), { 'Content-Type': 'application/json' });

        return {
            async consultar({ categoria, marca, q, page, perPage }) {
                const parametros = new URLSearchParams({
                    categoria: String(categoria),
                    marca: String(marca),
                    page: String(page),
                    perPage: String(perPage),
                });
                if (q) parametros.set('q', q);

                const r = await fetch(`${app.dataset.urlListar}?${parametros.toString()}`, {
                    credentials: 'same-origin',
                  
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!r.ok) throw new Error(await leerError(r));

                const datos = await r.json();

                // El backend no manda "color": es solo un dato visual para la
                // miniatura placeholder cuando el producto no tiene imagen todavía.
                return {
                    ...datos,
                    items: datos.items.map((p) => ({ ...p, color: PALETA[p.lote % PALETA.length] })),
                };
            },

                        // Toggle "Activo": ruta propia (no deja registro en la bitácora de lotes).
            async cambiarActivo(id, activo) {
                try {
                    await postJson(app.dataset.urlCambiarActivo, { id, activo });
                    return true;
                } catch (err) {
                    console.error('[masivo] error al cambiar activo', err);
                    return false;
                }
            },

            async crearTanda({ categoria, marca, token, precio, stock, archivos }) {
                if (tandasProcesadas.has(token)) return tandasProcesadas.get(token);

                const form = new FormData();
                form.append('categoria', categoria);
                form.append('marca', marca);
                form.append('token', token);
                form.append('precio', precio);
                form.append('stock', stock);
                archivos.forEach((f) => form.append('archivos[]', f));

                const resultado = await post(app.dataset.urlCrear, form);
                tandasProcesadas.set(token, resultado);
                return resultado;
            },

            async previsualizarBorrado(ids) {
                return postJson(app.dataset.urlPrevisualizarBorrado, { ids });
            },

            async editar(ids, cambios) {
                return postJson(app.dataset.urlEditar, { ids, ...cambios });
            },

            async eliminar(ids) {
                return postJson(app.dataset.urlEliminar, { ids });
            },
        };
    })();

    /* ───────────────────── 3. Estado y referencias al DOM ───────────────────── */

    const el = {
        categoria: $('ctxCategoria'),
        marca: $('ctxMarca'),
        resumen: $('ctxResumen'),
        badge: $('ctxBadge'),
        total: $('ctxTotal'),
        proximoLote: $('ctxProximoLote'),
        btnCargar: $('btnCargar'),

        buscar: $('masivoBuscar'),
        limpiar: $('masivoLimpiar'),
        porPagina: $('masivoPorPagina'),

        barra: $('barraSeleccion'),
        selCount: $('selCount'),
        selTexto: $('selTexto'),
        btnSelFiltro: $('btnSelTodosFiltro'),
        selTotalFiltro: $('selTotalFiltro'),
        btnLimpiarSel: $('btnLimpiarSel'), // opcional (ver nota en el Blade)
        btnEditarSel: $('btnEditarSel'),
        btnEliminarSel: $('btnEliminarSel'),

        tabla: $('tabla_masivo'),
        tbody: $('tbodyMasivo'),
        chkTodos: $('chkTodos'),

        estadoInicial: $('estadoInicial'),
        estadoSinProductos: $('estadoSinProductos'),
        estadoSinResultados: $('estadoSinResultados'),

        paginacion: app.querySelector('.custom-pagination-container'),
        pgDesde: $('pgDesde'),
        pgHasta: $('pgHasta'),
        pgTotal: $('pgTotal'),
        pgLista: $('pgLista'),
        pgPrev: $('pgPrev'),
        pgNext: $('pgNext'),

        resultado: $('masivoResultado'),

        // Modal Eliminar
        modalEliminar: $('modalEliminar'),
        delMensaje: $('delMensaje'),
        delCarrusel: $('delCarrusel'),
        delLista: $('delLista'),
        delInput: $('delInput'),
        delConfirmar: $('delConfirmar'),
        delCancelar: $('delCancelar'),
        delCerrarX: $('delCerrarX'),
        delSpinner: $('delSpinner'),
        delIcono: $('delIcono'),

        // Modal Editar
        modalEditar: $('modalEditar'),
        edSubtitulo: $('edSubtitulo'),
        edPrecio: $('edPrecio'),
        edStockModo: $('edStockModo'),
        edStock: $('edStock'),
        edActivo: $('edActivo'),
        edDestacado: $('edDestacado'),
        edDescAccion: $('edDescAccion'),
        edDescCampos: $('edDescCampos'),
        edDescPrecio: $('edDescPrecio'),
        edDescInicio: $('edDescInicio'),
        edDescFin: $('edDescFin'),
        edError: $('edError'),
        edAviso: $('edAviso'),
        edAplicar: $('edAplicar'),
        edCancelar: $('edCancelar'),
        edCerrarX: $('edCerrarX'),
        edSpinner: $('edSpinner'),
        edIcono: $('edIcono'),

        // Modal Cargar imágenes
        modalCargar: $('modalCargar'),
        cgSubtitulo: $('cgSubtitulo'),
        cgFormulario: $('cgFormulario'),
        cgPrecio: $('cgPrecio'),
        cgStock: $('cgStock'),
        cgZona: $('cgZona'),
        cgArchivo: $('cgArchivo'),
        cgLista: $('cgLista'),
        cgResumen: $('cgResumen'),
        cgQuitarTodas: $('cgQuitarTodas'),
        cgAviso: $('cgAviso'),
        cgError: $('cgError'),
        cgProgreso: $('cgProgreso'),
        cgProgresoTexto: $('cgProgresoTexto'),
        cgProgresoPct: $('cgProgresoPct'),
        cgBarra: $('cgBarra'),
        cgCancelar: $('cgCancelar'),
        cgCrear: $('cgCrear'),
        cgCrearTexto: $('cgCrearTexto'),
        cgSpinner: $('cgSpinner'),
        cgCerrarX: $('cgCerrarX'),
    };
    el.tablaWrap = el.tabla.closest('.table-responsive');

    const estado = {
        categoria: '',
        marca: '',
        q: '',
        page: 1,
        perPage: parseInt(el.porPagina.value, 10) || 50,

        seleccion: new Set(), // ids seleccionados; vive fuera del DOM
        idsConVariantes: new Set(),
        idsFiltrados: [],     // ids de todo el filtro actual
        idsPagina: [],        // ids de las filas visibles
        totalFiltrado: 0,
        totalPar: 0,
        proximoLote: 1,
    };

    let peticion = 0; // para descartar respuestas viejas si llegan tarde

    const contextoListo = () => Boolean(estado.categoria && estado.marca);
    const textoSeleccionado = (select) => select.options[select.selectedIndex]?.text.trim() ?? '';

    /* ───────────────────────── 4. Render ───────────────────────── */

    function miniatura(p) {
        if (p.imagen) {
            return `<img src="${esc(p.imagen)}" class="img-thumb" loading="lazy" alt="${esc(p.nombre)}">`;
        }
        return `<div class="img-thumb d-flex align-items-center justify-content-center small fw-semibold"
                     style="background:${esc(p.color)};color:#fff">${pad4(p.lote)}</div>`;
    }

     function precioHtml(p) {
        if (p.usaVariantes) return `<span class="text-muted small">Según variantes</span>`;
        if (p.precioDescuento) {
            const pct = p.precio > 0 ? Math.round(((p.precio - p.precioDescuento) / p.precio) * 100) : 0;
            return `<div class="masivo-precio">
                        <span class="masivo-precio-etiqueta"><i class="bx bxs-offer"></i> Oferta -${pct}%</span>
                        <span class="masivo-precio-final">${colones(p.precioDescuento)}</span>
                        <span class="masivo-precio-antes">${colones(p.precio)}</span>
                    </div>`;
        }
        return `<span class="masivo-precio-normal">${colones(p.precio)}</span>`;
    }

    function stockHtml(p) {
        if (p.usaVariantes) return `<span class="text-muted small">Según variantes</span>`;
        return p.stock > 0
            ? `<span class="order-badge">${p.stock}</span>`
            : `<span class="status-badge status-inactive"><i class="bx bx-package me-1"></i>Agotado</span>`;
    }

    function destacadoHtml(p) {
        return p.destacado
            ? `<span class="status-badge status-active"><i class="bx bx-star me-1"></i>Destacado</span>`
            : `<span class="status-badge status-inactive">Normal</span>`;
    }

    function filaHtml(p) {
        const marcada = estado.seleccion.has(p.id);
        return `
        <tr data-id="${p.id}" class="${marcada ? 'fila-seleccionada' : ''}">
            <td><input type="checkbox" class="form-check-input chk-fila" data-id="${p.id}" ${marcada ? 'checked' : ''}></td>
            <td class="text-start">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0">${miniatura(p)}</div>
                    <div>
                        <div class="fw-semibold">${esc(p.nombre)}</div>
                                           <small class="text-muted">slug: ${esc(p.slug)}</small>
                        ${p.usaVariantes ? '<span class="status-badge status-inactive ms-1">Variantes</span>' : ''}
                    </div>
                </div>
            </td>
            <td class="fw-semibold">${precioHtml(p)}</td>
            <td>${stockHtml(p)}</td>
            <td>
                <div class="form-check form-switch d-flex justify-content-center m-0">
                    <input class="form-check-input chk-activo" type="checkbox" role="switch"
                           data-id="${p.id}" ${p.activo ? 'checked' : ''} title="Activar / desactivar">
                </div>
            </td>
            <td>${destacadoHtml(p)}</td>
            <td>
                <div class="d-flex justify-content-center gap-2">
                    <a class="btn-action btn-view" title="Ver"
                       href="${esc(urlConId(app.dataset.urlVer, p.id))}"><i class="bx bx-show"></i></a>
                    <a class="btn-action btn-edit" title="Editar"
                       href="${esc(urlConId(app.dataset.urlEditarUno, p.id))}"><i class="bx bx-edit"></i></a>
                </div>
            </td>
        </tr>`;
    }

    // Muestra solo uno de: 'inicial' | 'sinProductos' | 'sinResultados' | 'tabla'
    function mostrarModo(modo) {
        const conTabla = modo === 'tabla';
        el.tablaWrap.classList.toggle('d-none', !conTabla);
        el.paginacion.classList.toggle('d-none', !conTabla);
        el.estadoInicial.classList.toggle('d-none', modo !== 'inicial');
        el.estadoSinProductos.classList.toggle('d-none', modo !== 'sinProductos');
        el.estadoSinResultados.classList.toggle('d-none', modo !== 'sinResultados');
    }

    function renderContexto(proximoLote) {
        const listo = contextoListo();

        el.btnCargar.disabled = !listo;
        el.buscar.disabled = !listo;
        el.porPagina.disabled = !listo;
        el.resumen.classList.toggle('d-none', !listo);

        if (listo) {
            el.badge.textContent = `${textoSeleccionado(el.categoria)} · ${textoSeleccionado(el.marca)}`.toUpperCase();
            el.total.textContent = estado.totalPar;
            el.proximoLote.textContent = '#' + pad4(proximoLote ?? 1);
        }
    }

    function renderPaginacion() {
        const { totalFiltrado: total, perPage, page } = estado;
        const paginas = Math.max(1, Math.ceil(total / perPage));

        el.pgDesde.textContent = total === 0 ? 0 : (page - 1) * perPage + 1;
        el.pgHasta.textContent = Math.min(total, page * perPage);
        el.pgTotal.textContent = total;

        el.pgLista.querySelectorAll('.page-number, .page-ellipsis').forEach((n) => n.remove());

        let fin = Math.min(paginas, Math.max(1, page - 2) + 4);
        let ini = Math.max(1, fin - 4);

        const frag = document.createDocumentFragment();
        const elipsis = () => {
            const li = document.createElement('li');
            li.className = 'page-item disabled page-ellipsis';
            li.innerHTML = '<span class="page-link">...</span>';
            return li;
        };

        if (ini > 1) frag.append(elipsis());
        for (let i = ini; i <= fin; i++) {
            const li = document.createElement('li');
            li.className = 'page-item page-number' + (i === page ? ' active' : '');
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            frag.append(li);
        }
        if (fin < paginas) frag.append(elipsis());

        el.pgLista.insertBefore(frag, el.pgNext);
        el.pgPrev.classList.toggle('disabled', page <= 1);
        el.pgNext.classList.toggle('disabled', page >= paginas);
    }

    function renderSeleccion() {
        const n = estado.seleccion.size;
        el.barra.classList.toggle('d-none', n === 0);
        el.selCount.textContent = n;
        el.selTexto.textContent = n === 1 ? 'producto seleccionado' : 'productos seleccionados';

        const marcadasEnPagina = estado.idsPagina.filter((id) => estado.seleccion.has(id)).length;
        const paginaCompleta = estado.idsPagina.length > 0 && marcadasEnPagina === estado.idsPagina.length;

        el.chkTodos.checked = paginaCompleta;
        el.chkTodos.indeterminate = marcadasEnPagina > 0 && !paginaCompleta;

        const filtroCompleto =
            estado.idsFiltrados.length > 0 && estado.idsFiltrados.every((id) => estado.seleccion.has(id));
        const hayMasFueraDeLaPagina = estado.idsFiltrados.length > estado.idsPagina.length;

        el.selTotalFiltro.textContent = estado.idsFiltrados.length;
        el.btnSelFiltro.classList.toggle('d-none', !(paginaCompleta && hayMasFueraDeLaPagina && !filtroCompleto));
        if (el.btnLimpiarSel) el.btnLimpiarSel.classList.toggle('d-none', n === 0);
    }

    function pintarSeleccionEnFilas() {
        el.tbody.querySelectorAll('tr[data-id]').forEach((tr) => {
            const marcada = estado.seleccion.has(Number(tr.dataset.id));
            tr.classList.toggle('fila-seleccionada', marcada);
            const chk = tr.querySelector('.chk-fila');
            if (chk) chk.checked = marcada;
        });
        renderSeleccion();
    }

    /* ───────────────────────── 5. Carga ───────────────────────── */

    async function cargar() {
        if (!contextoListo()) {
            estado.idsFiltrados = [];
            estado.idsPagina = [];
            estado.totalFiltrado = 0;
            estado.totalPar = 0;
            el.tbody.innerHTML = '';
            renderContexto();
            renderPaginacion();
            renderSeleccion();
            mostrarModo('inicial');
            return;
        }

        const mia = ++peticion;
        app.classList.add('is-loading');

        try {
            const r = await Fuente.consultar({
                categoria: estado.categoria,
                marca: estado.marca,
                q: estado.q,
                page: estado.page,
                perPage: estado.perPage,
            });

            if (mia !== peticion) return; // llegó una respuesta vieja: se ignora

            estado.page = r.pagina;
            estado.totalFiltrado = r.total;
            estado.totalPar = r.totalPar;
            estado.proximoLote = r.proximoLote;
            estado.idsFiltrados = r.idsFiltrados;
            estado.idsConVariantes = new Set(r.idsConVariantes ?? []);
            estado.idsPagina = r.items.map((p) => p.id);

            const dentro = new Set(r.idsFiltrados);
            estado.seleccion.forEach((id) => { if (!dentro.has(id)) estado.seleccion.delete(id); });

            el.tbody.innerHTML = r.items.map(filaHtml).join('');
            renderContexto(r.proximoLote);
            renderPaginacion();
            renderSeleccion();

            if (r.totalPar === 0) mostrarModo('sinProductos');
            else if (r.total === 0) mostrarModo('sinResultados');
            else mostrarModo('tabla');
        } catch (err) {
            console.error('[masivo] error al consultar', err);
        } finally {
            if (mia === peticion) app.classList.remove('is-loading');
        }
    }

    /* ───────────────────────── 6. Eventos ───────────────────────── */

    function alCambiarContexto() {
        estado.categoria = el.categoria.value;
        estado.marca = el.marca.value;
        estado.q = '';
        estado.page = 1;
        estado.seleccion.clear();
        el.buscar.value = '';
        cargar();
    }
    el.categoria.addEventListener('change', alCambiarContexto);
    el.marca.addEventListener('change', alCambiarContexto);

    const buscarConDemora = debounce(() => {
        estado.q = el.buscar.value;
        estado.page = 1;
        cargar();
    }, 250);
    el.buscar.addEventListener('input', buscarConDemora);
    el.limpiar.addEventListener('click', () => {
        el.buscar.value = '';
        estado.q = '';
        estado.page = 1;
        cargar();
        el.buscar.focus();
    });

    el.porPagina.addEventListener('change', () => {
        estado.perPage = parseInt(el.porPagina.value, 10) || 50;
        estado.page = 1;
        cargar();
    });

    el.pgLista.addEventListener('click', (e) => {
        const enlace = e.target.closest('a.page-link');
        if (!enlace) return;
        e.preventDefault();
        if (enlace.closest('.disabled') || enlace.closest('.active')) return;

        if (enlace.closest('#pgPrev')) estado.page -= 1;
        else if (enlace.closest('#pgNext')) estado.page += 1;
        else if (enlace.dataset.page) estado.page = parseInt(enlace.dataset.page, 10);
        else return;

        cargar();
    });

    el.tbody.addEventListener('change', async (e) => {
        const chk = e.target.closest('.chk-fila');
        if (chk) {
            const id = Number(chk.dataset.id);
            chk.checked ? estado.seleccion.add(id) : estado.seleccion.delete(id);
            chk.closest('tr').classList.toggle('fila-seleccionada', chk.checked);
            renderSeleccion();
            return;
        }

        const sw = e.target.closest('.chk-activo');
        if (sw) {
            const previo = !sw.checked;
            const ok = await Fuente.cambiarActivo(Number(sw.dataset.id), sw.checked);
            if (!ok) sw.checked = previo; // si falla, vuelve al valor anterior
        }
    });

    el.chkTodos.addEventListener('change', () => {
        estado.idsPagina.forEach((id) => (el.chkTodos.checked ? estado.seleccion.add(id) : estado.seleccion.delete(id)));
        pintarSeleccionEnFilas();
    });

    el.btnSelFiltro.addEventListener('click', (e) => {
        e.preventDefault();
        estado.idsFiltrados.forEach((id) => estado.seleccion.add(id));
        pintarSeleccionEnFilas();
    });

    if (el.btnLimpiarSel) {
        el.btnLimpiarSel.addEventListener('click', (e) => {
            e.preventDefault();
            estado.seleccion.clear();
            pintarSeleccionEnFilas();
        });
    }

    /* ───────────────────── 7. Modales y resultado ───────────────────── */

    const datosContexto = () => ({
        categoria: estado.categoria,
        marca: estado.marca,
        categoriaNombre: textoSeleccionado(el.categoria),
        marcaNombre: textoSeleccionado(el.marca),
    });

    function bsModal(nodo) {
        if (!nodo || !window.bootstrap?.Modal) {
            console.warn('[masivo] Bootstrap JS no está disponible para abrir el modal');
            return null;
        }
        return window.bootstrap.Modal.getOrCreateInstance(nodo);
    }

    // `accion`: { texto, onClick } opcional — agrega un botón dentro del aviso
    // (lo usamos para el "Recargar página" cuando la sesión expiró).
    function mostrarResultado({ tipo, titulo, mensaje, errores = [], notas = [], accion = null }) {
        if (!el.resultado) return;
        const clase = { ok: 'success', parcial: 'warning', error: 'danger' }[tipo] || 'info';
        const detalle = errores.length
            ? `<ul class="mb-0 mt-2 small">${errores.map((e) => `<li>${esc(e.nombre ?? e.id)}: ${esc(e.motivo)}</li>`).join('')}</ul>`
            : '';

        const extra = notas.map((n) => `<div class="small mt-1">${esc(n)}</div>`).join('');
        const boton = accion
            ? `<div class="mt-2"><button type="button" class="btn btn-sm btn-outline-dark" id="resultadoAccionBtn">${esc(accion.texto)}</button></div>`
            : '';

        el.resultado.innerHTML = `
            <div class="alert alert-${clase} alert-dismissible fade show mb-3" role="alert">
                <strong>${esc(titulo)}</strong> ${esc(mensaje)}
                ${extra}
                ${detalle}
                ${boton}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>`;
        el.resultado.classList.remove('d-none');
        el.resultado.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        if (accion) $('resultadoAccionBtn')?.addEventListener('click', accion.onClick);
    }

    /* ---- 7.1 Eliminar selección ---- */

    const borrado = { ids: [], enCurso: false };
    const PALABRA_BORRAR = 'BORRAR';

   const palabraCorrecta = () => el.delInput.value.trim().toUpperCase() === PALABRA_BORRAR;
    const actualizarBotonBorrar = () => { el.delConfirmar.disabled = borrado.enCurso || !palabraCorrecta(); };

    function ponerBorrandoEnCurso(enCurso) {
        borrado.enCurso = enCurso;
        el.delSpinner.classList.toggle('d-none', !enCurso);
        el.delIcono.classList.toggle('d-none', enCurso);
        el.delCancelar.disabled = enCurso;
        el.delCerrarX.disabled = enCurso;
        el.delInput.disabled = enCurso;
        actualizarBotonBorrar();
    }

       async function abrirEliminar() {
        const ids = [...estado.seleccion];
        if (!ids.length) return;

        const modal = bsModal(el.modalEliminar);
        if (!modal) return;

        borrado.ids = ids;

        let prev;
        try {
            prev = await Fuente.previsualizarBorrado(ids);
        } catch (err) {
            console.error('[masivo] error al previsualizar borrado', err);
            mostrarResultado(
                esErrorSesion(err)
                    ? { tipo: 'error', titulo: 'Tu sesión expiró.', mensaje: 'Recargá la página para seguir trabajando.', accion: accionRecargar() }
                    : { tipo: 'error', titulo: 'No se pudo abrir la vista previa.', mensaje: err.message || 'Intentá de nuevo en unos segundos.' }
            );
            return;
        }

        const n = prev.total;
        el.delMensaje.innerHTML =
            `Vas a borrar <b>${n}</b> ${n === 1 ? 'producto' : 'productos'} de forma permanente, junto con sus ` +
            `imágenes, movimientos de inventario y favoritos asociados. ` +
            `Los pedidos ya hechos no se ven afectados.`;

        // NUEVO (cambio 3.10): aviso de variantes
        if (prev.conVariantes) {
            el.delMensaje.innerHTML +=
                `<br><b>${prev.conVariantes}</b> ${prev.conVariantes === 1 ? 'tiene' : 'tienen'} variantes, que también se borrarán.`;
        }

        el.delLista.innerHTML =
            prev.muestra.map((nombre) => `<li>${esc(nombre)}</li>`).join('') +
            (n > prev.muestra.length ? `<li class="text-muted">y ${n - prev.muestra.length} más…</li>` : '');

        el.delCarrusel.classList.toggle('d-none', !prev.enCarrusel);
        if (prev.enCarrusel) {
            el.delCarrusel.textContent =
                `${prev.enCarrusel} de estos productos aparecen en el carrusel; esos banners se desactivarán al borrar.`;
        }

        el.delInput.value = '';
        ponerBorrandoEnCurso(false);
        modal.show();
    }

    async function confirmarEliminar() {
        if (borrado.enCurso || !palabraCorrecta()) return;
        ponerBorrandoEnCurso(true);

        try {
            const r = await Fuente.eliminar(borrado.ids);
            (r.idsEliminados ?? borrado.ids).forEach((id) => estado.seleccion.delete(id));

            bsModal(el.modalEliminar)?.hide();
            await cargar();

            const fallaron = r.errores.length;
            mostrarResultado({
                tipo: fallaron ? 'parcial' : 'ok',
                titulo: fallaron ? 'Borrado parcial.' : 'Listo.',
                mensaje: `Solicitados: ${r.solicitados} · eliminados: ${r.ok}` + (fallaron ? ` · con error: ${fallaron}.` : '.'),
                errores: r.errores,
            });
        } catch (err) {
            console.error('[masivo] error al eliminar', err);
            bsModal(el.modalEliminar)?.hide();
            mostrarResultado(
                esErrorSesion(err)
                    ? { tipo: 'error', titulo: 'Tu sesión expiró.', mensaje: 'Recargá la página; revisá si algunos productos ya se eliminaron antes del corte.', accion: accionRecargar() }
                    : { tipo: 'error', titulo: 'No se pudo completar el borrado.', mensaje: err.message || 'Revisá la lista: algunos productos podrían haberse eliminado antes del error.' }
            );
        } finally {
            ponerBorrandoEnCurso(false);
        }
    }

    if (el.modalEliminar) {
        el.delInput.addEventListener('input', actualizarBotonBorrar);
        el.delInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); confirmarEliminar(); }
        });
        el.delConfirmar.addEventListener('click', confirmarEliminar);
        el.modalEliminar.addEventListener('shown.bs.modal', () => el.delInput.focus());
    }
    el.btnEliminarSel.addEventListener('click', abrirEliminar);

    /* ---- 7.2 Editar selección ---- */

    const edicion = { ids: [], enCurso: false };
    const ETIQUETA_CAMPO = { precio: 'precio', stock: 'stock', activo: 'estado', destacado: 'destacado', descuento: 'descuento' };

     // Solo los campos con casilla (el descuento ya no tiene: usa un selector propio).
    const edCampos = () => [...el.modalEditar.querySelectorAll('.ed-campo')].filter((c) => c.querySelector('.ed-check'));
    const edMarcado = (campo) =>
        campo === 'descuento'
            ? el.edDescAccion.value !== ''
            : el.modalEditar.querySelector(`[data-campo="${campo}"] .ed-check`).checked;
    const edHayMarcados = () => edCampos().some((c) => c.querySelector('.ed-check').checked) || edMarcado('descuento');

    function edActualizarBoton() {
        el.edAplicar.disabled = edicion.enCurso || !edHayMarcados();
    }

    function edMostrarError(msg) {
        el.edError.textContent = msg ?? '';
        el.edError.classList.toggle('d-none', !msg);
    }

    function edActualizarAviso() {
        const afectados = edicion.ids.filter((id) => estado.idsConVariantes.has(id)).length;
        const toca = edMarcado('precio') || edMarcado('stock') || edMarcado('descuento');
        const mostrar = afectados > 0 && toca;

        el.edAviso.textContent = mostrar
            ? `${afectados} ${afectados === 1 ? 'producto seleccionado tiene' : 'productos seleccionados tienen'} variantes y se omitirán: su precio, stock y descuento se editan en cada variante.`
            : '';
        el.edAviso.classList.toggle('d-none', !mostrar);
    }

    function edAlternarCampo(campoEl) {
        const activo = campoEl.querySelector('.ed-check').checked;
        campoEl.classList.toggle('activo', activo);
        campoEl.querySelectorAll('.ed-control').forEach((c) => { c.disabled = !activo; });
    }

     function edAlternarDescuento() {
        el.edDescCampos.classList.toggle('d-none', el.edDescAccion.value !== 'activar');
    }
    function edEnCurso(enCurso) {
        edicion.enCurso = enCurso;
        el.edSpinner.classList.toggle('d-none', !enCurso);
        el.edIcono.classList.toggle('d-none', enCurso);
        el.edCancelar.disabled = enCurso;
        el.edCerrarX.disabled = enCurso;
        edActualizarBoton();
    }

    function edLeerCambios() {
        const cambios = {};

        if (edMarcado('precio')) {
            const v = Number(el.edPrecio.value);
            if (el.edPrecio.value === '' || !Number.isFinite(v) || v <= 0) {
                throw new Error('El precio debe ser un número mayor que 0.');
            }
            cambios.precio = Math.round(v);
        }

        if (edMarcado('stock')) {
            const modo = el.edStockModo.value;
            const v = Number(el.edStock.value);
            if (el.edStock.value === '' || !Number.isInteger(v) || v < 0) {
                throw new Error('El stock debe ser un número entero, sin decimales ni negativos.');
            }
            if (modo !== 'fijar' && v === 0) {
                throw new Error('Para sumar o restar stock indicá una cantidad mayor que 0.');
            }
            cambios.stock = { modo, valor: v };
        }

        if (edMarcado('activo')) cambios.activo = el.edActivo.value === '1';
        if (edMarcado('destacado')) cambios.destacado = el.edDestacado.value === '1';

        if (edMarcado('descuento')) {
            if (el.edDescAccion.value === 'quitar') {
                cambios.descuento = { accion: 'quitar' };
            } else {
                const precio = Number(el.edDescPrecio.value);
                if (el.edDescPrecio.value === '' || !Number.isFinite(precio) || precio <= 0) {
                    throw new Error('El precio de descuento debe ser un número mayor que 0.');
                }
                const inicio = el.edDescInicio.value;
                const fin = el.edDescFin.value;
                if (!inicio || !fin) throw new Error('Indicá desde cuándo y hasta cuándo rige el descuento.');
                if (new Date(fin) <= new Date(inicio)) throw new Error('La fecha de fin debe ser posterior a la de inicio.');
                if (cambios.precio !== undefined && precio >= cambios.precio) {
                    throw new Error('El precio de descuento debe ser menor que el nuevo precio.');
                }
                cambios.descuento = { accion: 'activar', precio: Math.round(precio), inicio, fin };
            }
        }

        if (!Object.keys(cambios).length) throw new Error('Marcá al menos un campo para modificar.');
        return cambios;
    }

    function abrirEditar() {
        const ids = [...estado.seleccion];
        if (!ids.length) return;

        const modal = bsModal(el.modalEditar);
        if (!modal) return;

        edicion.ids = ids;
        const n = ids.length;
        el.edSubtitulo.textContent =
            `${n} ${n === 1 ? 'producto seleccionado' : 'productos seleccionados'} — solo se aplican los campos marcados`;

        edCampos().forEach((c) => { c.querySelector('.ed-check').checked = false; edAlternarCampo(c); });
        [el.edPrecio, el.edStock, el.edDescPrecio, el.edDescInicio, el.edDescFin].forEach((i) => { i.value = ''; });
        [el.edStockModo, el.edActivo, el.edDestacado, el.edDescAccion].forEach((sel) => { sel.selectedIndex = 0; });
        edAlternarDescuento();
        edMostrarError('');
        edActualizarAviso();
        edEnCurso(false);

        modal.show();
    }

    async function aplicarEdicion() {
        if (edicion.enCurso) return;

        let cambios;
        try {
            cambios = edLeerCambios();
        } catch (err) {
            edMostrarError(err.message);
            return;
        }
        edMostrarError('');
        edEnCurso(true);

        try {
            const r = await Fuente.editar(edicion.ids, cambios);

            bsModal(el.modalEditar)?.hide();
            await cargar();

            const campos = Object.keys(cambios).map((k) => ETIQUETA_CAMPO[k]).join(', ');
            const fallaron = r.errores.length;
            mostrarResultado({
                tipo: !fallaron ? 'ok' : r.ok > 0 ? 'parcial' : 'error',
                titulo: !fallaron ? 'Listo.' : r.ok > 0 ? 'Edición parcial.' : 'No se aplicó ningún cambio.',
                mensaje: `Campos: ${campos} · solicitados: ${r.solicitados} · actualizados: ${r.ok}` +
                         (fallaron ? ` · con error: ${fallaron}.` : '.'),
                errores: r.errores,
                notas: r.avisos ?? [],
            });
        } catch (err) {
            console.error('[masivo] error al editar', err);
            bsModal(el.modalEditar)?.hide();
            mostrarResultado(
                esErrorSesion(err)
                    ? { tipo: 'error', titulo: 'Tu sesión expiró.', mensaje: 'Recargá la página; revisá si algunos productos ya se actualizaron antes del corte.', accion: accionRecargar() }
                    : { tipo: 'error', titulo: 'No se pudo completar la edición.', mensaje: err.message || 'Revisá la lista: algunos productos podrían haberse actualizado antes del error.' }
            );
        } finally {
            edEnCurso(false);
        }
    }

    if (el.modalEditar) {
        el.modalEditar.addEventListener('change', (e) => {
            const check = e.target.closest('.ed-check');
            if (check) edAlternarCampo(check.closest('.ed-campo'));
            if (e.target === el.edDescAccion) edAlternarDescuento();
            edMostrarError('');
            edActualizarBoton();
            edActualizarAviso();
        });
        el.modalEditar.addEventListener('input', () => edMostrarError(''));
        el.edAplicar.addEventListener('click', aplicarEdicion);
    }
    el.btnEditarSel.addEventListener('click', abrirEditar);

/* ---- 7.3 Cargar imágenes ---- */

    // La carga se parte en tandas porque php.ini limita max_file_uploads (20 por defecto)
    // y el tamaño total del POST (post_max_size, 40M en este equipo).
    // Cada tanda lleva como máximo 20 imágenes Y 30 MB; lo que se cumpla primero.
    const CARGA = {
        maxArchivos: 100,
        maxMB: 8,
        tanda: 20,
        maxBytesTanda: 30 * 1048576, // 30 MB por tanda (debe quedar por debajo de post_max_size)
        tipos: ['image/jpeg', 'image/png', 'image/webp'],
    };
    
    const carga = { archivos: [], claves: new Set(), enCurso: false, detener: false };

    const cgClave = (f) => `${f.name}|${f.size}|${f.lastModified}`;
    const cgMB = (bytes) => (bytes / 1048576).toFixed(1);
    const cgToken = () =>
        (window.crypto?.randomUUID ? window.crypto.randomUUID() : `${Date.now()}-${Math.random()}`);

    function cgMensaje(nodo, texto) {
        nodo.textContent = texto ?? '';
        nodo.classList.toggle('d-none', !texto);
    }

    function cgRender() {
        const n = carga.archivos.length;

        el.cgLista.innerHTML = carga.archivos
            .map(
                (f, i) => `
                <span class="cg-chip" title="${esc(f.name)} · ${cgMB(f.size)} MB">
                    <span class="cg-chip-nombre">${esc(f.name)}</span>
                    <button type="button" class="cg-chip-x" data-i="${i}" aria-label="Quitar ${esc(f.name)}">&times;</button>
                </span>`
            )
            .join('');

        el.cgResumen.textContent = n
            ? `${n} ${n === 1 ? 'imagen seleccionada' : 'imágenes seleccionadas'} (máximo ${CARGA.maxArchivos}). Se numeran en el orden mostrado, por nombre.`
            : '';
        el.cgQuitarTodas.classList.toggle('d-none', n === 0 || carga.enCurso);
        el.cgCrearTexto.textContent = n ? `Crear ${n} ${n === 1 ? 'producto' : 'productos'}` : 'Crear productos';
        el.cgCrear.disabled = carga.enCurso || n === 0;
    }

    function cgAgregar(archivos) {
        const rechazados = [];
        let sobrantes = 0;

        for (const f of archivos) {
            if (carga.claves.has(cgClave(f))) continue;
            if (!CARGA.tipos.includes(f.type)) { rechazados.push(`${f.name}: solo se aceptan JPG, PNG o WEBP`); continue; }
            if (f.size === 0) { rechazados.push(`${f.name}: el archivo está vacío`); continue; }
            if (f.size > CARGA.maxMB * 1048576) {
                rechazados.push(`${f.name}: pesa ${cgMB(f.size)} MB (máximo ${CARGA.maxMB} MB)`);
                continue;
            }
            if (carga.archivos.length >= CARGA.maxArchivos) { sobrantes++; continue; }

            carga.archivos.push(f);
            carga.claves.add(cgClave(f));
        }

        if (sobrantes) rechazados.push(`${sobrantes} ${sobrantes === 1 ? 'imagen no entró' : 'imágenes no entraron'}: el máximo es ${CARGA.maxArchivos} por carga`);

        carga.archivos.sort((a, b) => a.name.localeCompare(b.name, undefined, { numeric: true, sensitivity: 'base' }));
        cgRender();

        const visibles = rechazados.slice(0, 5);
        const resto = rechazados.length - visibles.length;
        const omitidos = rechazados.length - (sobrantes ? 1 : 0) + sobrantes;
        cgMensaje(el.cgAviso, rechazados.length
            ? `Se omitieron ${omitidos} ${omitidos === 1 ? 'archivo' : 'archivos'}: ${visibles.join(' · ')}${resto > 0 ? ` · y ${resto} más` : ''}`
            : '');
    }

    function cgQuitar(indice) {
        const [f] = carga.archivos.splice(indice, 1);
        if (f) carga.claves.delete(cgClave(f));
        cgRender();
    }

    function cgProgreso(hechos, total, texto) {
        const pct = total ? Math.round((hechos / total) * 100) : 0;
        el.cgBarra.style.width = pct + '%';
        el.cgBarra.setAttribute('aria-valuenow', String(pct));
        el.cgProgresoPct.textContent = pct + '%';
        if (texto) el.cgProgresoTexto.textContent = texto;
    }

    function cgModoCarga(enCurso) {
        carga.enCurso = enCurso;
        el.cgFormulario.classList.toggle('d-none', enCurso);
        el.cgProgreso.classList.toggle('d-none', !enCurso);
        el.cgSpinner.classList.toggle('d-none', !enCurso);
        el.cgCerrarX.disabled = enCurso;
        el.cgCancelar.disabled = false;
        el.cgCancelar.textContent = enCurso ? 'Detener' : 'Cancelar';
        cgRender();
    }

    function abrirCargar() {
        if (!contextoListo()) return;
        const modal = bsModal(el.modalCargar);
        if (!modal) return;

        carga.archivos = [];
        carga.claves.clear();
        carga.detener = false;

        el.cgSubtitulo.innerHTML =
            `Contexto: <b>${esc(textoSeleccionado(el.categoria))} · ${esc(textoSeleccionado(el.marca))}</b>` +
            ` — próximo número: <b>#${pad4(estado.proximoLote)}</b>`;

        el.cgPrecio.value = '';
        el.cgStock.value = '1';
        el.cgArchivo.value = '';
        cgMensaje(el.cgAviso, '');
        cgMensaje(el.cgError, '');
        cgProgreso(0, 0, '');
        cgModoCarga(false);

        modal.show();
    }

    async function crearLote() {
        if (carga.enCurso) return;

        const precio = Number(el.cgPrecio.value);
        const stock = Number(el.cgStock.value);

        if (el.cgPrecio.value === '' || !Number.isFinite(precio) || precio <= 0) {
            return cgMensaje(el.cgError, 'Escribí el precio que se aplicará a todo el lote (mayor que 0).');
        }
        if (el.cgStock.value === '' || !Number.isInteger(stock) || stock < 0) {
            return cgMensaje(el.cgError, 'El stock debe ser un número entero, 0 o mayor.');
        }
        if (!carga.archivos.length) return cgMensaje(el.cgError, 'Agregá al menos una imagen.');
        cgMensaje(el.cgError, '');

        const archivos = [...carga.archivos];
        const total = archivos.length;

          // Se parte en tandas por cantidad (máx. 20) Y por peso total (máx. 30 MB),
        // para no pasarse del post_max_size de PHP.
        const tandas = [];
        let actual = [];
        let pesoActual = 0;
        for (const f of archivos) {
            const sePasa =
                actual.length >= CARGA.tanda ||
                (actual.length > 0 && pesoActual + f.size > CARGA.maxBytesTanda);
            if (sePasa) {
                tandas.push(actual);
                actual = [];
                pesoActual = 0;
            }
            actual.push(f);
            pesoActual += f.size;
        }
        if (actual.length) tandas.push(actual);


        const ctx = datosContexto();
        const errores = [];
        let creados = 0;
        let procesados = 0;
        let primerLote = null;
        let ultimoLote = null;
        let detenido = false;
        let sesionExpirada = false;

        carga.detener = false;
        cgModoCarga(true);
        cgProgreso(0, total, `Preparando ${tandas.length} ${tandas.length === 1 ? 'tanda' : 'tandas'}…`);

        for (let t = 0; t < tandas.length; t++) {
            if (carga.detener || sesionExpirada) { detenido = true; break; }
            cgProgreso(procesados, total, `Subiendo tanda ${t + 1} de ${tandas.length} · ${procesados}/${total} imágenes`);

            try {
                const r = await Fuente.crearTanda({
                    categoria: ctx.categoria,
                    marca: ctx.marca,
                    marcaNombre: ctx.marcaNombre,
                    token: cgToken(),
                    precio: Math.round(precio),
                    stock,
                    archivos: tandas[t],
                });
                creados += r.ok;
                errores.push(...r.errores);
                if (r.primerLote !== null) {
                    if (primerLote === null) primerLote = r.primerLote;
                    ultimoLote = r.ultimoLote;
                }
            } catch (err) {
                console.error('[masivo] falló una tanda', err);
                // Si la sesión expiró, no tiene sentido seguir mandando el resto
                // de las tandas: todas van a fallar igual. Se corta el ciclo.
                if (esErrorSesion(err)) {
                    sesionExpirada = true;
                    tandas.slice(t).forEach((tanda) =>
                        tanda.forEach((f) => errores.push({ nombre: f.name, motivo: err.message }))
                    );
                    procesados += tandas[t].length;
                    break;
                }
                tandas[t].forEach((f) => errores.push({ nombre: f.name, motivo: err.message || 'falló la tanda (red o servidor)' }));
            }

            procesados += tandas[t].length;
            cgProgreso(procesados, total);
        }

        cgModoCarga(false);
        bsModal(el.modalCargar)?.hide();

        estado.q = '';
        el.buscar.value = '';
        estado.page = Number.MAX_SAFE_INTEGER;
        await cargar();

        const sinProcesar = total - procesados;
        const notas = [];
        if (primerLote !== null) notas.push(`Lotes creados: #${pad4(primerLote)} a #${pad4(ultimoLote)}.`);
        if (sesionExpirada) notas.push('Tu sesión expiró durante la carga. Recargá la página e intentá de nuevo con las imágenes restantes.');
        else if (detenido) notas.push(`Se detuvo la carga: ${sinProcesar} ${sinProcesar === 1 ? 'imagen no se procesó' : 'imágenes no se procesaron'}.`);

        const hayProblemas = errores.length > 0 || detenido;
        mostrarResultado({
            tipo: !hayProblemas ? 'ok' : creados > 0 ? 'parcial' : 'error',
            titulo: !hayProblemas ? 'Listo.' : creados > 0 ? (detenido ? 'Carga detenida.' : 'Carga parcial.') : 'No se creó ningún producto.',
            mensaje: `Solicitadas: ${total} · creados: ${creados}` + (errores.length ? ` · con error: ${errores.length}.` : '.'),
            errores,
            notas,
            accion: sesionExpirada ? accionRecargar() : null,
        });
    }

    if (el.modalCargar) {
        const zona = el.cgZona;
        zona.addEventListener('click', () => el.cgArchivo.click());
        zona.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); el.cgArchivo.click(); }
        });
        ['dragenter', 'dragover'].forEach((tipo) =>
            zona.addEventListener(tipo, (e) => { e.preventDefault(); zona.classList.add('arrastrando'); }));
        ['dragleave', 'drop'].forEach((tipo) =>
            zona.addEventListener(tipo, (e) => { e.preventDefault(); zona.classList.remove('arrastrando'); }));
        zona.addEventListener('drop', (e) => cgAgregar([...(e.dataTransfer?.files ?? [])]));

        el.cgArchivo.addEventListener('change', () => {
            cgAgregar([...el.cgArchivo.files]);
            el.cgArchivo.value = '';
        });

        el.cgLista.addEventListener('click', (e) => {
            const x = e.target.closest('.cg-chip-x');
            if (x) cgQuitar(Number(x.dataset.i));
        });
        el.cgQuitarTodas.addEventListener('click', (e) => {
            e.preventDefault();
            carga.archivos = [];
            carga.claves.clear();
            cgAgregar([]);
        });

        el.modalCargar.addEventListener('input', () => cgMensaje(el.cgError, ''));
        el.cgCrear.addEventListener('click', crearLote);

        el.cgCancelar.addEventListener('click', () => {
            if (carga.enCurso) {
                carga.detener = true;
                el.cgCancelar.disabled = true;
                el.cgCancelar.textContent = 'Deteniendo…';
            } else {
                bsModal(el.modalCargar)?.hide();
            }
        });
    }
    el.btnCargar.addEventListener('click', abrirCargar);

    // Arranque: sin contexto elegido muestra el estado inicial
    cargar();
})();

