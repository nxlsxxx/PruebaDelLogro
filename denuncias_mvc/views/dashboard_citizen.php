<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bienvenido, Ciudadano!</h2>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Tus Denuncias Recientes
        </div>
        <div class="card-body">
            <?php if (!empty($citizenDenuncias)): ?>
                <ul class="list-group">
                    <?php foreach ($citizenDenuncias as $denuncia): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($denuncia['titulo']); ?></strong> - <small><?php echo htmlspecialchars($denuncia['fecha_registro']); ?></small><br>
                                <p class="mb-0">Estado: <span class="badge bg-info"><?php echo htmlspecialchars($denuncia['estado']); ?></span></p>
                            </div>
                            <a href="index.php?controller=Denuncia&action=view&id=<?php echo $denuncia['id']; ?>" class="btn btn-sm btn-info">Ver Detalles</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($totalDenuncias > 10): // Simple pagination link if more than 10 ?>
                    <div class="mt-3 text-center">
                        <a href="index.php?controller=Denuncia&action=index&user_id=<?php echo $userId; ?>" class="btn btn-primary">Ver Todas tus Denuncias</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>No has registrado ninguna denuncia aún. <a href="#" data-bs-toggle="modal" data-bs-target="#denunciaFormModal">¡Crea una ahora!</a></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            Notificaciones (Próximamente)
        </div>
        <div class="card-body">
            <p>Aquí verás actualizaciones sobre el estado de tus denuncias.</p>
            <p class="text-muted">Funcionalidad de notificaciones en desarrollo.</p>
        </div>
    </div>
</div>
