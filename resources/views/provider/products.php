<?php ob_start(); ?>

<h2 class="label-sys fs-5 mb-4 border-bottom border-dark pb-2">Mis Productos</h2>

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

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="glass-panel p-0" style="background: var(--bg-white); border: 1px solid #000;">
            <div class="bg-success text-white p-2 border-bottom border-dark">
                <span class="fw-bold small">REGISTRAR NUEVO PRODUCTO</span>
            </div>
            <div class="p-4">
                <form action="/proveedor/productos/agregar" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre del Producto</label>
                        <input type="text" class="form-control glass-input" name="name" placeholder="Ej. Laptop Pro 15"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea class="form-control glass-input" name="description" rows="3"
                            placeholder="Detalles del artículo..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Precio ($)</label>
                            <input type="number" step="0.01" class="form-control glass-input" name="price" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Existencias</label>
                            <input type="number" class="form-control glass-input" name="stock_quantity" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Imagen del Producto</label>
                        <input type="file" class="form-control glass-input" name="image" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-hard btn-success w-100 fw-bold">GUARDAR PRODUCTO</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="glass-panel-dark p-0 border border-dark">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-light text-dark">
                        <tr>
                            <th class="small">ID</th>
                            <th class="small">VISTA</th>
                            <th class="small">DETALLES</th>
                            <th class="small">STOCK</th>
                            <th class="small">FECHA</th>
                            <th class="small text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay productos en tu inventario.</td>
                        </tr>
                        <?php
else:
    foreach ($products as $product): ?>
                        <tr>
                            <td class="font-monospace">
                                <?= $product['id']?>
                            </td>
                            <td>
                                <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= $product['image_url']?>"
                                    style="max-height: 40px; border: 1px solid #555;">
                                <?php
        else: ?>
                                <span class="text-muted" style="font-size: 0.7rem;">S/I</span>
                                <?php
        endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-white">
                                    <?= $product['name']?>
                                </div>
                                <div class="text-success small">$
                                    <?= number_format($product['price'], 2)?>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="badge rounded-0 <?= $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger text-white'?>">
                                    <?= $product['stock_quantity']?> disp.
                                </span>
                            </td>
                            <td class="small text-secondary">
                                <?= date('d/m/y', strtotime($product['created_at']))?>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-outline-light btn-sm px-3" onclick="openEditProductModal(
                                        <?= $product['id']?>, 
                                        '<?= addslashes(htmlspecialchars($product['name']))?>', 
                                        '<?= addslashes(htmlspecialchars($product['description']))?>', 
                                        <?= $product['price']?>, 
                                        <?= $product['stock_quantity']?>, 
                                        '<?= $product['image_url'] ?? ''?>')">
                                        Editar
                                    </button>
                                    <form action="/proveedor/productos/eliminar" method="POST"
                                        onsubmit="return confirm('¿Está seguro de eliminar este producto? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                                        <input type="hidden" name="product_id" value="<?= $product['id']?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php
    endforeach;
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-dark shadow-lg">
            <div class="modal-header bg-dark text-white rounded-0">
                <h5 class="modal-title fs-6">EDITAR INFORMACIÓN DEL PRODUCTO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light text-dark">
                <form id="editProductForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">

                    <div class="mb-3 text-center d-none" id="edit_p_preview_container">
                        <img id="edit_p_preview" src="" style="max-height: 100px; border: 2px solid #ddd;" class="mb-2">
                        <p class="small text-muted">Imagen actual</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" class="form-control" id="edit_p_name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea class="form-control" id="edit_p_desc" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Precio ($)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_p_price" name="price"
                                required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Existencias</label>
                            <input type="number" class="form-control" id="edit_p_stock" name="stock_quantity" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Cambiar imagen (Opcional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-dark w-100 fw-bold">ACTUALIZAR DATOS</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function openEditProductModal(id, name, desc, price, stock, image_url) {
        document.getElementById('editProductForm').action = '/proveedor/productos/' + id + '/editar';
        document.getElementById('edit_p_name').value = name;
        document.getElementById('edit_p_desc').value = desc;
        document.getElementById('edit_p_price').value = price;
        document.getElementById('edit_p_stock').value = stock;
        const imgPreview = document.getElementById('edit_p_preview');
        const container = document.getElementById('edit_p_preview_container');
        if (image_url) {
            imgPreview.src = image_url;
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
        new bootstrap.Modal(document.getElementById('editProductModal')).show();
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>