{{-- resources/views/admin/partials/modal-eliminar.blade.php --}}
<div class="modal fade" id="modalEliminar" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-gradient-danger text-white border-0 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="warning-icon-pulse">
                        <i class="bx bx-error-circle fs-4"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-0">Confirmar eliminación</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-4">

                <div class="text-center mb-4">
                    <div class="alert-icon mb-3">
                        <div class="icon-circle bg-danger-soft">
                            <i class="bx bx-trash fs-1 text-danger"></i>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2">¿Seguro que deseas eliminar este registro?</h6>
                    <p class="text-muted small mb-3">
                      
                    </p>
                </div>

                <div class="info-card bg-light rounded-3 p-3 mb-3">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bx bx-data fs-4 text-primary mt-1"></i>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <label class="text-muted small text-uppercase fw-semibold mb-1 d-block"></label>
                                <div class="fw-bold text-dark" id="labelClave">-</div>
                            </div>

                            <div>
                                <label class="text-muted small text-uppercase fw-semibold mb-1 d-block"></label>
                                <div class="text-secondary" id="labelValor">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning border-0 py-2 px-3 small mb-0">
                    <i class="bx bx-info-circle me-1"></i>
                   Esta acción es irreversible. Una vez realizada, los cambios serán permanentes y no se podrán deshacer.
                </div>

            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-3">

                {{-- Cancelar --}}
                <button type="button" 
                        class="btn btn-secondary-custom"
                        data-bs-dismiss="modal">
                 
                    Cancelar
                </button>

                {{-- Eliminar --}}
                <button type="button" 
                        class="btn btn-danger-custom"
                        id="btnConfirmarEliminar">
             
                    Sí, eliminar
                </button>

            </div>

        </div>
    </div>
</div>