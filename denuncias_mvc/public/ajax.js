// public/ajax.js

function loadDenuncias(page = 1, searchQuery = '', userRole = null, userId = null) {
    const tableBody = document.querySelector('#denunciasTable tbody');
    const paginationContainer = document.querySelector('.pagination');

    fetch(`index.php?controller=Denuncia&action=buscarAjax&page=${page}&search=${searchQuery}`)
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = ''; // Clear existing rows
            if (data.denuncias.length > 0) {
                data.denuncias.forEach(denuncia => {
                    let actionButtons = '';
                    // Conditionally render edit/delete buttons
                    if (userRole === 'ingeniero' || (userRole === 'ciudadano' && denuncia.user_id == userId)) {
                        actionButtons = `
                            <button type="button" class="btn btn-warning btn-sm btn-editar" data-bs-toggle="modal" data-bs-target="#denunciaFormModal" data-id="${denuncia.id}"><i class="bi bi-pencil-square"></i></button>
                            <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="${denuncia.id}"><i class="bi bi-trash"></i></button>
                        `;
                    }

                    const row = `
                        <tr>
                            <td>
                                <div class="table-actions">
                                    ${actionButtons}
                                </div>
                            </td>
                            <td>${denuncia.id}</td>
                            <td>${denuncia.titulo}</td>
                            <td>${denuncia.descripcion}</td>
                            <td>${denuncia.ubicacion}</td>
                            <td>${denuncia.ciudadano}</td>
                            <td>${denuncia.fecha_registro}</td>
                            <td>${denuncia.estado}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="8">No hay denuncias para mostrar.</td></tr>';
            }
            updatePagination(data.currentPage, data.totalPages, searchQuery, userRole, userId);
        })
        .catch(error => console.error('Error loading denuncias:', error));
}

function updatePagination(currentPage, totalPages, searchQuery, userRole, userId) {
    const paginationContainer = document.querySelector('.pagination');
    paginationContainer.innerHTML = ''; // Clear existing pagination

    let paginationHtml = `
        <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}" data-search="${searchQuery}">Anterior</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        paginationHtml += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}" data-search="${searchQuery}">${i}</a>
            </li>
        `;
    }

    paginationHtml += `
        <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}" data-search="${searchQuery}">Siguiente</a>
        </li>
    `;

    paginationContainer.innerHTML = paginationHtml;

    // Add event listeners to new pagination links
    paginationContainer.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute('data-page'));
            const search = this.getAttribute('data-search');
            if (!isNaN(page) && page > 0 && page <= totalPages) {
                loadDenuncias(page, search, userRole, userId);
            }
        });
    });
}

// Initial load on DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get current user role and ID from meta tags or a global JS variable set by PHP
    const currentUserRole = document.querySelector('meta[name="user-role"]') ? document.querySelector('meta[name="user-role"]').getAttribute('content') : null;
    const currentUserId = document.querySelector('meta[name="user-id"]') ? parseInt(document.querySelector('meta[name="user-id"]').getAttribute('content')) : null;
    const currentUserName = document.querySelector('meta[name="user-name"]') ? document.querySelector('meta[name="user-name"]').getAttribute('content') : null;
    const currentUserPhone = document.querySelector('meta[name="user-phone"]') ? document.querySelector('meta[name="user-phone"]').getAttribute('content') : null;

    // Debugging: Log frontend user data
    console.log("DEBUG Frontend: User Role:", currentUserRole);
    console.log("DEBUG Frontend: User ID:", currentUserId);

    if (currentUserId) {
        loadDenuncias(1, '', currentUserRole, currentUserId);
    }

    // Hide 'Nuevo' button if not logged in or if the user is a citizen (citizens create from their profile)
    const btnCrearDenuncia = document.getElementById('btnCrearDenuncia');
    if (btnCrearDenuncia) {
        if (!currentUserId) {
            btnCrearDenuncia.style.display = 'none';
        }
    }

    // Search form submission
    const searchForm = document.querySelector('#denunciaSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchQuery = document.querySelector('#searchInput').value;
            loadDenuncias(1, searchQuery, currentUserRole, currentUserId);
        });
    }

    // Handle form submission for creating/editing
    const denunciaForm = document.getElementById('denunciaForm');
    const modalTitle = document.getElementById('denunciaFormModalLabel');
    const idInput = document.getElementById('denunci-id');

    const tituloInput = document.getElementById('titulo');
    const descripcionInput = document.getElementById('descripcion');
    const ubicacionInput = document.getElementById('ubicacion');
    const ciudadanoInput = document.getElementById('ciudadano');
    const telefonoCiudadanoInput = document.getElementById('telefono_ciudadano');
    const estadoInput = document.getElementById('estado');

    // Flag to ensure the form submission listener is only attached once
    let formListenerAttached = false;

    if (btnCrearDenuncia) {
        btnCrearDenuncia.addEventListener('click', function() {
            modalTitle.textContent = 'Nuevo Reporte de Denuncia';
            denunciaForm.reset();
            idInput.value = '';
            denunciaForm.action = 'index.php?controller=Denuncia&action=guardar';

            // Auto-fill for citizens
            if (currentUserRole === 'ciudadano') {
                ciudadanoInput.value = currentUserName || '';
                telefonoCiudadanoInput.value = currentUserPhone || '';
                estadoInput.value = 'Pendiente'; // Default status for citizen
                
                // Disable citizen and phone fields for citizens
                ciudadanoInput.readOnly = true;
                telefonoCiudadanoInput.readOnly = true;
                estadoInput.disabled = true;
            } else {
                // Ensure fields are editable for engineers/guests
                ciudadanoInput.readOnly = false;
                telefonoCiudadanoInput.readOnly = false;
                estadoInput.disabled = false;
                estadoInput.value = 'Pendiente'; // Default status for engineer/guest
            }
        });
    }

    if (denunciaForm && !formListenerAttached) {
        denunciaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false; // Re-enable button
                if (data.success) {
                    var denunciaFormModal = bootstrap.Modal.getInstance(document.getElementById('denunciaFormModal'));
                    denunciaFormModal.hide();
                    loadDenuncias(1, document.querySelector('#searchInput').value, currentUserRole, currentUserId); // Reload list after save
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al guardar la denuncia:', error);
                submitButton.disabled = false; // Re-enable button on error
            });
        });
        formListenerAttached = true; // Set flag after attaching listener
    }

    // Delegate click event for edit/delete buttons (since they are dynamically added)
    const denunciasTableBody = document.querySelector('#denunciasTable tbody');
    if (denunciasTableBody) {
        denunciasTableBody.addEventListener('click', function(e) {
            if (e.target.closest('.btn-eliminar')) {
                const denunciaIdToDelete = e.target.closest('.btn-eliminar').getAttribute('data-id');
                document.getElementById('btnConfirmDelete').setAttribute('data-id', denunciaIdToDelete);
            } else if (e.target.closest('.btn-editar')) {
                const button = e.target.closest('.btn-editar');
                const denunciaId = button.getAttribute('data-id');
                const form = document.getElementById('denunciaForm');
                const modalTitle = document.getElementById('denunciaFormModalLabel');
                const idInput = document.getElementById('denunci-id');

                modalTitle.textContent = 'Editar Denuncia';
                form.reset(); // Clear previous form data
                idInput.value = denunciaId;
                form.action = 'index.php?controller=Denuncia&action=actualizar';

                fetch(`index.php?controller=Denuncia&action=obtenerPorId&id=${denunciaId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("DEBUG: Data fetched for editing:", data); // Added debug log
                        if (data) {
                            tituloInput.value = data.titulo;
                            descripcionInput.value = data.descripcion;
                            ubicacionInput.value = data.ubicacion;

                            // Conditional field access for citizens
                            if (currentUserRole === 'ciudadano') {
                                ciudadanoInput.value = data.ciudadano || currentUserName || '';
                                telefonoCiudadanoInput.value = data.telefono_ciudadano || currentUserPhone || '';
                                estadoInput.value = data.estado;

                                // Disable citizen and phone fields for citizens
                                ciudadanoInput.readOnly = true;
                                telefonoCiudadanoInput.readOnly = true;

                                // Disable status field if already set and not pending for citizens
                                if (data.estado !== 'Pendiente') {
                                    estadoInput.disabled = true;
                                } else {
                                    estadoInput.disabled = false; // Allow citizen to set to pendiente if not set
                                }
                            } else {
                                // Engineer/Guest: all fields editable
                                ciudadanoInput.value = data.ciudadano || '';
                                telefonoCiudadanoInput.value = data.telefono_ciudadano || '';
                                estadoInput.value = data.estado;

                                ciudadanoInput.readOnly = false;
                                telefonoCiudadanoInput.readOnly = false;
                                estadoInput.disabled = false;
                            }

                        } else {
                            console.error('Error: No se encontraron datos para la denuncia con ID:', denunciaId);
                        }
                    })
                    .catch(error => console.error('Error al obtener datos de la denuncia:', error));
            }
        });
    }

    // Handle confirm delete
    const btnConfirmDelete = document.getElementById('btnConfirmDelete');
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function() {
            const denunciaIdToDelete = this.getAttribute('data-id');
            if (denunciaIdToDelete) {
                const formData = new FormData();
                formData.append('id', denunciaIdToDelete);

                fetch('index.php?controller=Denuncia&action=destroy', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optionally show a temporary success message
                        // alert(data.message);
                        var confirmDeleteModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                        confirmDeleteModal.hide();
                        loadDenuncias(1, document.querySelector('#searchInput').value, currentUserRole, currentUserId); // Reload current page after deletion
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error al eliminar la denuncia:', error));
            }
        });
    }

    // Close sidebar when clicking outside on small screens
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.sidebar');
        const navbarToggler = document.querySelector('.navbar-toggler'); // Assuming you have a navbar-toggler button

        if (sidebar && navbarToggler && !sidebar.contains(event.target) && !navbarToggler.contains(event.target) && sidebar.classList.contains('show')) {
            // Use Bootstrap's native JS to hide the collapse element
            const bsCollapse = new bootstrap.Collapse(sidebar, { toggle: false });
            bsCollapse.hide();
        }
    });

    // Explicitly initialize Bootstrap modals to ensure proper aria-hidden management
    const denunciaFormModalElement = document.getElementById('denunciaFormModal');
    if (denunciaFormModalElement) {
        new bootstrap.Modal(denunciaFormModalElement);
    }

    const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
    if (confirmDeleteModalElement) {
        new bootstrap.Modal(confirmDeleteModalElement);
    }
});
