<?php ob_start(); ?>

<h2 class="mb-4">Mi Inventario (Panel de Proveedor)</h2>

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

<div class="row">
    <!-- Formulario para agregar producto -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Agregar Producto</h5>
            </div>
            <div class="card-body">
                <form action="/proveedor/productos/agregar" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Precio ($)</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock_quantity" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Guardar Producto</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de productos existentes -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No tienes productos enlistados.</td>
                            </tr>
                            <?php
else: ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?= $product['id']?>
                                </td>
                                <td>
                                    <?= $product['name']?>
                                </td>
                                <td>$
                                    <?= $product['price']?>
                                </td>
                                <td>
                                    <span
                                        class="badge <?= $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'?>">
                                        <?= $product['stock_quantity']?> u.
                                    </span>
                                </td>
                                <td>
                                    <form action="/proveedor/productos/eliminar" method="POST"
                                        onsubmit="return confirm('¿Eliminar este producto?');">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                                        <input type="hidden" name="product_id" value="<?= $product['id']?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
    endforeach; ?>
                            <?php
endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>