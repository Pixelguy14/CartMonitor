<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6 mb-4">
        <h2 class="mb-4">Mi Perfil</h2>

        <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= $success?>
        </div>
        <?php
endif; ?>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= $error?>
        </div>
        <?php
endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="/perfil/editar" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" name="username" value="<?= $user['username']?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" value="<?= $user['email']?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="phone" value="<?= $user['phone']?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña <small class="text-muted">(Dejar en blanco para
                                mantener la actual)</small></label>
                        <input type="password" class="form-control" name="password" minlength="6">
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Actualizar Mi Perfil</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Advertencia</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Si eliminas tu cuenta, no podrás recuperarla. Las órdenes asociadas a tu cuenta se
                    mantendrán anonimizadas para el historial administrativo.</p>
                <form action="/perfil/eliminar" method="POST"
                    onsubmit="return confirm('¿Estás SEGURO de que deseas eliminar permanentemente tu cuenta? Esta acción no se puede deshacer.');">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                    <button type="submit" class="btn btn-outline-danger w-100">Eliminar Mi Cuenta</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>