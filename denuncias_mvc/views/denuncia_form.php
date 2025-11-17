<div class="modal fade" id="denunciaFormModal" tabindex="-1" aria-labelledby="denunciaFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="denunciaFormModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="denunciaForm" action="index.php?controller=Denuncia&action=guardar" method="POST">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" id="denunci-id" name="id">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ingrese título">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Ingrese descripción"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ingrese ubicación (ej. coordenadas o dirección)">
                    </div>
                    <div class="mb-3">
                        <label for="ciudadano" class="form-label">Ciudadano</label>
                        <input type="text" class="form-control" id="ciudadano" name="ciudadano" placeholder="Ingrese nombre del ciudadano">
                    </div>
                    <div class="mb-3">
                        <label for="telefono_ciudadano" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono_ciudadano" name="telefono_ciudadano" placeholder="Ingrese teléfono del ciudadano">
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control" id="estado" name="estado">
                            <option value="">Seleccione un estado</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En proceso">En proceso</option>
                            <option value="Resuelto">Resuelto</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

