<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6 mb-4">
        <h2 class="label-sys fs-5 mb-4 border-bottom border-dark pb-2">Mi Perfil</h2>

        <?php if (!empty($success)): ?>
        <div class="alert alert-success rounded-0 border-dark">
            <?= $success?>
        </div>
        <?php
endif; ?>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-0 border-dark">
            <?= $error?>
        </div>
        <?php
endif; ?>

        <div class="glass-panel p-0 mb-4" style="background: var(--bg-white);">
            <div class="p-4">
                <form action="/perfil/editar" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3">
                        <label class="form-label font-monospace small fw-bold">Nombre de Usuario</label>
                        <input type="text" class="form-control glass-input" name="username"
                            value="<?= $user['username']?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-monospace small fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control glass-input" name="email" value="<?= $user['email']?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-monospace small fw-bold">Teléfono</label>
                        <input type="text" class="form-control glass-input" name="phone" value="<?= $user['phone']?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-monospace small fw-bold">Nueva Contraseña <small
                                class="text-muted">(Dejar en blanco para mantener actual)</small></label>
                        <input type="password" class="form-control glass-input" name="password" minlength="6">
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-hard btn-primary w-100">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="glass-panel p-0" style="background: var(--bg-white);">
            <div class="bg-danger text-dark p-2 border-bottom border-dark">
                <span class="label-sys mb-0 text-white">Eliminar Cuenta</span>
            </div>
            <div class="p-4">
                <p class="font-monospace small text-muted text-uppercase">Si eliminas tu cuenta, no podrás recuperarla.
                    Las órdenes asociadas a tu cuenta se mantendrán anonimizadas para el historial administrativo.</p>
                <form action="/perfil/eliminar" method="POST"
                    onsubmit="return confirm('¿Estás completamente seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                    <button type="submit" class="btn btn-hard btn-danger w-100">Eliminar Permanentemente</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>