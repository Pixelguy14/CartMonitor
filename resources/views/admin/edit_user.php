<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm mt-5">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Editar Usuario:
                    <?= $user['username']?>
                </h3>

                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= $error?>
                </div>
                <?php
endif; ?>

                <form method="POST" action="/admin/usuarios/<?= $user['id']?>/editar">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de usuario</label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="<?= $user['username']?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $user['email']?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="<?= $user['phone'] ?? ''?>">
                    </div>

                    <hr>
                    <p class="text-muted small mb-3">Deja el campo de contraseña en blanco si no deseas modificarla.</p>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva Contraseña (Opcional)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/admin/usuarios" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>