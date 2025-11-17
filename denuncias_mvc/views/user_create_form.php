<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Crear Nuevo Usuario</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form action="index.php?controller=User&action=create" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                </div>
                <div class="mb-3">
                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                </div>
                <div class="mb-3">
                    <label for="dni" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="dni" name="dni" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                </div>
                <div class="mb-3">
                    <label for="telefono_ciudadano" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono_ciudadano" name="telefono_ciudadano">
                </div>
                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol</label>
                    <select class="form-control" id="rol_id" name="rol_id" required>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo htmlspecialchars($rol['id']); ?>"><?php echo htmlspecialchars($rol['nombre_rol']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
            </form>
        </div>
    </div>
</div>
