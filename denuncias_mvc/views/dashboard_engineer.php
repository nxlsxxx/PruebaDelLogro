<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bienvenido, Ingeniero de Sistemas!</h2>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Denuncias Pendientes
        </div>
        <div class="card-body">
            <?php if (!empty($pendingDenuncias)): ?>
                <ul class="list-group">
                    <?php foreach ($pendingDenuncias as $denuncia): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($denuncia['titulo']); ?></strong> - <small>Por <?php echo htmlspecialchars($denuncia['ciudadano']); ?></small><br>
                                <p class="mb-0">Ubicación: <?php echo htmlspecialchars($denuncia['ubicacion']); ?></p>
                            </div>
                            <a href="index.php?controller=Denuncia&action=view&id=<?php echo $denuncia['id']; ?>" class="btn btn-sm btn-info">Ver</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-3 text-center">
                    <a href="index.php?controller=Denuncia&action=index&status=Pendiente" class="btn btn-primary">Ver Todas las Pendientes</a>
                </div>
            <?php else: ?>
                <p>No hay denuncias pendientes.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            Nuevos Usuarios
        </div>
        <div class="card-body">
            <?php if (!empty($newUsers)): ?>
                <ul class="list-group">
                    <?php foreach ($newUsers as $user): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno']); ?></strong> - <small><?php echo htmlspecialchars($user['rol']); ?></small><br>
                                <p class="mb-0">Registrado el: <?php echo htmlspecialchars($user['fecha_registro']); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-3 text-center">
                    <a href="index.php?controller=User&action=listUsers" class="btn btn-success">Ver Todos los Usuarios</a>
                </div>
            <?php else: ?>
                <p>No hay usuarios nuevos.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function loadEngineerDashboardData() {
            fetch('index.php?controller=Dashboard&action=getEngineerDashboardDataAjax')
                .then(response => response.json())
                .then(data => {
                    // Update Pending Denuncias
                    const pendingDenunciasContainer = document.querySelector('.card:first-of-type .card-body');
                    let pendingHtml = '';
                    if (data.pendingDenuncias && data.pendingDenuncias.length > 0) {
                        data.pendingDenuncias.forEach(denuncia => {
                            pendingHtml += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${denuncia.titulo}</strong> - <small>Por ${denuncia.ciudadano}</small><br>
                                        <p class="mb-0">Ubicación: ${denuncia.ubicacion}</p>
                                    </div>
                                    <a href="index.php?controller=Denuncia&action=view&id=${denuncia.id}" class="btn btn-sm btn-info">Ver</a>
                                </li>
                            `;
                        });
                        pendingHtml = `<ul class="list-group">${pendingHtml}</ul><div class="mt-3 text-center"><a href="index.php?controller=Denuncia&action=index&status=Pendiente" class="btn btn-primary">Ver Todas las Pendientes</a></div>`;
                    } else {
                        pendingHtml = '<p>No hay denuncias pendientes.</p>';
                    }
                    pendingDenunciasContainer.innerHTML = pendingHtml;

                    // Update New Users
                    const newUsersContainer = document.querySelector('.card:last-of-type .card-body');
                    let usersHtml = '';
                    if (data.newUsers && data.newUsers.length > 0) {
                        data.newUsers.forEach(user => {
                            usersHtml += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${user.nombre} ${user.apellido_paterno}</strong> - <small>${user.rol}</small><br>
                                        <p class="mb-0">Registrado el: ${user.fecha_registro}</p>
                                    </div>
                                </li>
                            `;
                        });
                        usersHtml = `<ul class="list-group">${usersHtml}</ul><div class="mt-3 text-center"><a href="index.php?controller=User&action=listUsers" class="btn btn-success">Ver Todos los Usuarios</a></div>`;
                    } else {
                        usersHtml = '<p>No hay usuarios nuevos.</p>';
                    }
                    newUsersContainer.innerHTML = usersHtml;
                })
                .catch(error => console.error('Error fetching engineer dashboard data:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            loadEngineerDashboardData();

            // Set up polling for real-time updates (e.g., every 30 seconds)
            setInterval(loadEngineerDashboardData, 30000);
        });
    </script>
</div>
