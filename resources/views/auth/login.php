<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm mt-5">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>

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
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    <p class="mt-3 text-center text-muted"><small>¿No tienes cuenta? <a href="/register">Regístrate
                                aquí</a></small></p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>