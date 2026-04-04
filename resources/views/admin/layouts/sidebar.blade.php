<!-- sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
	<div class="sidebar-header">
		<div>
			<img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
		</div>
		<div>
			<h4 class="logo-text">Rukada</h4>
		</div>
		<div class="toggle-icon ms-auto">
			<i class='bx bx-arrow-to-left'></i>
		</div>
	</div>

	<ul class="metismenu" id="menu">

		<!-- Dashboard -->
		<li>
			<a href="{{ route('admin.dashboard') }}">
				<div class="parent-icon"><i class='bx bx-home-circle'></i></div>
				<div class="menu-title">Dashboard</div>
			</a>
		</li>

		<li class="menu-label">Tienda</li>

		<!-- Carrusel -->
		<li>
			<a href="{{ route('admin.carrusel-items.index') }}">
				<div class="parent-icon"><i class='bx bx-carousel'></i></div>
				<div class="menu-title">Carrusel</div>
			</a>
		</li>

		<!-- Catálogos -->
		<li>
			<a href="javascript:;" class="has-arrow">
				<div class="parent-icon"><i class="bx bx-category"></i></div>
				<div class="menu-title">Catálogos</div>
			</a>
			<ul>
				<li>
					<a href="{{ route('admin.categorias.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Categorías
					</a>
				</li>
				<li>
					<a href="{{ route('admin.marcas.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Marcas
					</a>
				</li>
				<li>
					<a href="{{ route('admin.productos.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Productos
					</a>
				</li>
			</ul>
		</li>

		<!-- Ventas -->
		<li>
			<a href="javascript:;" class="has-arrow">
				<div class="parent-icon"><i class='bx bx-cart'></i></div>
				<div class="menu-title">Ventas</div>
			</a>
			<ul>
				<li>
					<a href="{{ route('admin.pedidos.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Ventas online (Pedidos)
					</a>
				</li>
				<li>
					<a href="{{ route('admin.ventas-locales.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Ventas físicas
					</a>
				</li>
				<li>
					<a href="{{ route('admin.ventas.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Reporte de ventas
					</a>
				</li>
			</ul>
		</li>

		<!-- Inventario -->
		<li>
			<a href="javascript:;" class="has-arrow">
				<div class="parent-icon"><i class='bx bx-box'></i></div>
				<div class="menu-title">Inventario</div>
			</a>
			<ul>
				<li>
					<a href="{{ route('admin.inventario-movimientos.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Movimientos
					</a>
				</li>
				<li>
					<a href="{{ route('admin.inventario-movimientos.create') }}">
						<i class="bx bx-right-arrow-alt"></i>Nuevo movimiento
					</a>
				</li>
			</ul>
		</li>

		<!-- Finanzas -->
		<li>
			<a href="javascript:;" class="has-arrow">
				<div class="parent-icon"><i class='bx bx-credit-card'></i></div>
				<div class="menu-title">Finanzas</div>
			</a>
			<ul>
				<li>
					<a href="{{ route('admin.pagos-pedidos.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Pagos pedidos
					</a>
				</li>
<li>
	<a href="{{ route('admin.pagos-ventas-locales.index') }}">
		<i class="bx bx-right-arrow-alt"></i>Pagos ventas locales
	</a>
</li>

				
				<li>
					<a href="{{ route('admin.usos-cupones.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Usos de cupones
					</a>
				</li>
			</ul>
		</li>

		<!-- Cupones -->
		<li>
			<a href="{{ route('admin.cupones.index') }}">
				<div class="parent-icon"><i class='bx bx-purchase-tag'></i></div>
				<div class="menu-title">Cupones</div>
			</a>
		</li>

		<!-- Zonas envío -->
		<li>
			<a href="{{ route('admin.zonas-envio.index') }}">
				<div class="parent-icon"><i class='bx bx-map'></i></div>
				<div class="menu-title">Zonas de envío</div>
			</a>
		</li>

		<li class="menu-label">Sistema</li>

		<!-- Usuarios / Roles -->
		<li>
			<a href="javascript:;" class="has-arrow">
				<div class="parent-icon"><i class='bx bx-user'></i></div>
				<div class="menu-title">Usuarios</div>
			</a>
			<ul>
				<li>
					<a href="{{ route('admin.usuarios.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Usuarios
					</a>
				</li>
				<li>
					<a href="{{ route('admin.roles.index') }}">
						<i class="bx bx-right-arrow-alt"></i>Roles
					</a>
				</li>
			</ul>
		</li>

		<!-- Configuración -->
		<li>
			<a href="{{ route('admin.configuracion.index') }}">
				<div class="parent-icon"><i class='bx bx-cog'></i></div>
				<div class="menu-title">Configuración</div>
			</a>
		</li>

	</ul>
</div>
<!-- end sidebar wrapper -->
