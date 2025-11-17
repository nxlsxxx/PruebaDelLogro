<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Denuncias</h2>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#denunciaFormModal" id="btnCrearDenuncia"><i class="bi bi-plus-circle"></i> Nuevo</button>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" id="denunciaSearchForm">
                <div class="col-md-10">
                    <input type="text" class="form-control" placeholder="Buscar..." id="searchInput">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-3" id="denunciasTable">
            <thead>
                <tr>
                    <th>Opciones</th>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Ubicación</th>
                    <th>Ciudadano</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>

            <?php /* Data will be loaded via AJAX */ ?>
            
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php /* Pagination links will be generated via AJAX */ ?>
        </ul>
    </nav>

    <!-- Include ajax.js after Bootstrap JS and your other scripts -->
    
</div>
