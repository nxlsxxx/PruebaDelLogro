<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-primary sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="d-flex align-items-center mb-3 ps-3 text-white">
            <i class="bi bi-patch-question fs-4 me-2"></i> <!-- Placeholder for logo icon -->
            <span class="fs-5 fw-semibold">PNL</span>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?controller=Dashboard&action=index">
                    <i class="bi bi-grid"></i>
                    Escritorio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="index.php?controller=Denuncia&action=index">
                    <i class="bi bi-file-earmark"></i>
                    Denuncias
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?controller=About&action=index">
                    <i class="bi bi-info-circle"></i>
                    Acerca de
                </a>
            </li>
            <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'ingeniero'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php?controller=User&action=showCreate">
                        <i class="bi bi-person-plus"></i>
                        Crear Usuarios
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
