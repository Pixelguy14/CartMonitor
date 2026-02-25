<?php ob_start(); ?>

<h2 class="mb-4">Gestión de Usuarios (Admin)</h2>

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

<!-- Lista de usuarios existentes -->
<div class="table-responsive">
    <table class="table table-striped table-hover bg-white shadow-sm rounded">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol Actual</th>
                <th>Acciones Rápidas (Rol)</th>
                <th>Gestión</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <?= $user['id']?>
                </td>
                <td>
                    <?= $user['username']?>
                </td>
                <td>
                    <?= $user['email']?>
                </td>
                <td>
                    <span
                        class="badge <?= $user['type'] === 'admin' ? 'bg-danger' : ($user['type'] === 'proveedor' ? 'bg-warning' : 'bg-info')?>">
                        <?= $user['type']?>
                    </span>
                </td>
                <td>
                    <form action="/admin/usuarios/rol" method="POST" class="d-flex gap-2">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                        <input type="hidden" name="user_id" value="<?= $user['id']?>">
                        <select name="type" class="form-select form-select-sm" style="width: auto;">
                            <option value="usuario" <?=$user['type']==='usuario' ? 'selected' : ''?>>Usuario</option>
                            <option value="proveedor" <?=$user['type']==='proveedor' ? 'selected' : ''?>>Proveedor
                            </option>
                            <option value="admin" <?=$user['type']==='admin' ? 'selected' : ''?>>Admin</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                    </form>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="/admin/usuarios/<?= $user['id']?>/editar" class="btn btn-warning btn-sm">Editar</a>
                        <form action="/admin/usuarios/eliminar" method="POST"
                            onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este usuario? Esta acción es irreversible.');">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                            <input type="hidden" name="user_id" value="<?= $user['id']?>">
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>