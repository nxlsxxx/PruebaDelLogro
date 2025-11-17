<?php
// Start session is handled in index.php
// Only include header, sidebar, and main content if a user is logged in
if (isset($_SESSION['user_id'])) {
    include "header.php";

    // Add meta tags for user data
    echo '<meta name="user-id" content="' . htmlspecialchars($_SESSION['user_id']) . '">';
    echo '<meta name="user-role" content="' . htmlspecialchars($_SESSION['user_rol']) . '">';
    echo '<meta name="user-name" content="' . htmlspecialchars($_SESSION['user_name']) . '">';
    // Assuming user_phone might be in session after login or retrieved from DB
    echo '<meta name="user-phone" content="' . htmlspecialchars($_SESSION['user_phone'] ?? '') . '">';
?>

<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row">
            <?php include "sidebar.php"; ?>

            <main class="col-12 col-md-9 offset-md-3 px-md-4 py-4 bg-white shadow-sm rounded">
                <?php echo $content; ?>
            </main>
        </div>
    </div>

    <?php include "footer.php"; ?>
    
    <!-- Edit/New Denuncia Form Modal moved to layout.php -->
    <?php include "denuncia_form.php"; ?>

    <!-- Delete confirmation modal moved to layout.php -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar esta denuncia?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDelete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
} else {
    // If not logged in, just render the content (login/register form)
    echo $content;
}
?>
<!-- Include ajax.js after Bootstrap JS and your other scripts -->
<script src="public/ajax.js"></script>
