{{-- resources/views/admin/layouts/scripts.blade.php --}}

<!-- =========================================
   BASE (DEPENDENCIAS PRINCIPALES)
========================================= -->

<!-- jQuery (SIEMPRE primero para plugins que lo usan) -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>

<!-- Bootstrap -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

<!-- SweetAlert2 (ANTES de tu JS global) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- =========================================
   📊 PLUGINS (DataTables, Charts, etc.)
========================================= -->

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>

<!-- Scroll / Menu -->
<script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>

<!-- Charts -->
<script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sparkline-charts/jquery.sparkline.min.js') }}"></script>

<!-- Knob -->
<script src="{{ asset('assets/plugins/jquery-knob/excanvas.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.js') }}"></script>
<script>
    $(function () {
        $(".knob").knob();
    });
</script>


<!-- =========================================
   ⚙️ JS DEL SISTEMA (LÓGICA GLOBAL)
========================================= -->

<!-- Tablas -->
<script src="{{ asset('assets/js/admin-table.js') }}"></script>

<!-- App general -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- Formularios + alertas + modal eliminar -->
<script src="{{ asset('assets/js/admin-forms.js') }}"></script>


<!-- =========================================
   📄 SCRIPTS POR VISTA
========================================= -->

@stack('scripts')

<!-- =========================================
   SWEETALERT DE PERFIL VALIDACIÓN
========================================= -->

@if (session('success'))

<script>

    Swal.fire({
        icon: 'success',
        title: 'Proceso completado',
        text: '{{ session('success') }}',
        confirmButtonColor: '#dca116',
        timer: 2800,
        timerProgressBar: true,
        showConfirmButton: false
    });

</script>

@if ($errors->any())

<script>

    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ $errors->first() }}',
        confirmButtonColor: '#d33',
    });

</script>

@endif

@endif