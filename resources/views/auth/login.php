<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass-panel mt-5">
            <div>
                <span class="label-sys text-center w-100 d-block mb-4">SYSTEM_LOGIN</span>

                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?>
                </div>
                <?php
endif; ?>

                <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8')?>
                </div>
                <?php
endif; ?>

                <form method="POST" action="/login">
                    <!-- CSRF Token Hidden Input -->
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8')?>">

                    <div class="mb-3">
                        <label for="email" class="form-label font-monospace small fw-bold">CORREO ELECTRÓNICO</label>
                        <input type="email" class="form-control glass-input" id="email" name="email" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label font-monospace small fw-bold">CONTRASEÑA</label>
                        <input type="password" class="form-control glass-input" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-hard btn-primary w-100 mb-3">INGRESAR</button>
                    <p class="mt-3 text-center text-muted font-monospace"><small>¿NO TIENES CUENTA? <a href="/register"
                                class="fw-bold">REGÍSTRATE
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