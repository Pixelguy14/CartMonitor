<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm mt-5">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Editar Producto:
                    <?= $product['name']?>
                </h3>

                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= $error?>
                </div>
                <?php
endif; ?>

                <form method="POST" action="/proveedor/productos/<?= $product['id']?>/editar"
                    enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="name" value="<?= $product['name']?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description"
                            rows="3"><?= $product['description']?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagen del Producto</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <?php if (!empty($product['image_url'])): ?>
                        <div class="mt-2">
                            <small class="text-muted">Imagen actual:</small><br>
                            <img src="<?= $product['image_url']?>" style="max-height: 100px; border-radius: 4px;">
                        </div>
                        <?php
endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Precio ($)</label>
                            <input type="number" step="0.01" class="form-control" name="price"
                                value="<?= $product['price']?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock_quantity"
                                value="<?= $product['stock_quantity']?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>