<?php ob_start(); ?>

<h2 class="label-sys fs-5 mb-4 border-bottom border-dark pb-2">Administrar Usuarios</h2>

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

<!-- Lista de usuarios existentes (The Ghost Palette) -->
<div class="table-responsive glass-panel-dark p-0 mb-4">
    <table class="table table-dark table-hover mb-0" style="background: transparent;">
        <thead style="border-bottom: 2px solid #334455;">
            <tr>
                <th class="font-monospace text-secondary">ID</th>
                <th class="font-monospace text-secondary">Usuario</th>
                <th class="font-monospace text-secondary">Correo (Email)</th>
                <th class="font-monospace text-secondary">Rol</th>
                <th class="font-monospace text-secondary">Cambiar Rol</th>
                <th class="font-monospace text-secondary">Acciones</th>
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
                    <span class="dr-toggle <?= $user['type'] === 'admin' ? 'active' : ''?>">
                        <span class="dr-box">
                            <?= $user['type'] === 'admin' ? '01' : '00'?>
                        </span>
                        <span class="dr-toggle-label" style="color: black; font-size: 0.6rem; padding: 2px 4px;">
                            <?= $user['type']?>
                        </span>
                    </span>
                </td>
                <td>
                    <form action="/admin/usuarios/rol" method="POST" class="d-flex gap-2">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                        <input type="hidden" name="user_id" value="<?= $user['id']?>">
                        <select name="type" class="form-select form-select-sm glass-input text-light"
                            style="width: auto; background: rgba(0,0,0,0.5);">
                            <option value="usuario" <?=$user['type']==='usuario' ? 'selected' : ''?>
                                class="text-dark">USUARIO</option>
                            <option value="proveedor" <?=$user['type']==='proveedor' ? 'selected' : ''?>
                                class="text-dark">PROVEEDOR
                            </option>
                            <option value="admin" <?=$user['type']==='admin' ? 'selected' : ''?>
                                class="text-dark">ADMIN</option>
                        </select>
                        <button type="submit" class="btn btn-hard btn-sm">Asignar</button>
                    </form>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <!-- Botón que abre el modal inyectando datos -->
                        <button type="button" class="btn btn-hard btn-sm" onclick="openEditModal(
                        <?= $user['id']?>, 
                        '<?= htmlspecialchars($user['username'], ENT_QUOTES)?>', 
                        '<?= htmlspecialchars($user['email'], ENT_QUOTES)?>', 
                        '<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES)?>')">
                            Editar
                        </button>
                        <form action="/admin/usuarios/eliminar" method="POST"
                            onsubmit="return confirm('¿Eliminar usuario permanentemente? Esta acción es irreversible.');">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                            <input type="hidden" name="user_id" value="<?= $user['id']?>">
                            <button type="submit" class="btn btn-hard btn-danger btn-sm">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>
</div>
</div>
</div>

<!-- Edit User Modal (Outside ob_start to avoid parent style nesting) -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-panel" style="background: var(--bg-white);">
            <div class="modal-header border-dark rounded-0">
                <h5 class="modal-title label-sys mb-0">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3">
                        <label for="edit_username" class="form-label font-monospace small fw-bold">Nombre de
                            Usuario</label>
                        <input type="text" class="form-control glass-input" id="edit_username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label font-monospace small fw-bold">Correo
                            Electrónico</label>
                        <input type="email" class="form-control glass-input" id="edit_email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_phone" class="form-label font-monospace small fw-bold">Teléfono</label>
                        <input type="text" class="form-control glass-input" id="edit_phone" name="phone">
                    </div>

                    <div class="mb-4">
                        <label for="edit_password" class="form-label font-monospace small fw-bold">Nueva Contraseña
                            (Opcional)</label>
                        <input type="password" class="form-control glass-input" id="edit_password" name="password"
                            placeholder="Dejar en blanco para mantener actual">
                    </div>

                    <button type="submit" class="btn btn-hard w-100">Actualizar Usuario</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, username, email, phone) {
        document.getElementById('editUserForm').action = '/admin/usuarios/' + id + '/editar';
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_phone').value = phone;
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>