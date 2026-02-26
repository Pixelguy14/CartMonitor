<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass-panel mt-5">
            <div>
                <span class="label-sys text-center w-100 d-block mb-4">SYSTEM_REGISTRATION</span>

                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?>
                </div>
                <?php
endif; ?>

                <form method="POST" action="/register">
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8')?>">

                    <div class="mb-3">
                        <label for="username" class="form-label font-monospace small fw-bold">IDENTIFICADOR
                            (USUARIO)</label>
                        <input type="text" class="form-control glass-input" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label font-monospace small fw-bold">CORREO ELECTRÓNICO</label>
                        <input type="email" class="form-control glass-input" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label font-monospace small fw-bold">CONTRASEÑA</label>
                        <input type="password" class="form-control glass-input" id="password" name="password" required>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label font-monospace small fw-bold">CONFIRMAR
                            CONTRASEÑA</label>
                        <input type="password" class="form-control glass-input" id="confirm_password"
                            name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-hard btn-success w-100 mb-3">REGISTRARSE</button>
                    <p class="mt-3 text-center text-muted font-monospace"><small>¿YA ESTÁS REGISTRADO? <a href="/login"
                                class="fw-bold">INICIA SESIÓN
                                AQUÍ</a></small></p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>