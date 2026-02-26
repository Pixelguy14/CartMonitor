<?php ob_start(); ?>

<h2 class="label-sys fs-5 mb-4 border-bottom border-dark pb-2">Mi Carrito</h2>

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

<?php if (empty($cartData['items'])): ?>
<div class="alert alert-info rounded-0 border-dark">El carrito está vacío. <a href="/" class="alert-link">Volver al
        Catálogo</a></div>
<?php
else: ?>
<div class="table-responsive glass-panel-dark p-0 mb-4">
    <table class="table table-dark table-hover mb-0" style="background:transparent;">
        <thead style="border-bottom: 2px solid #334455;">
            <tr>
                <th class="font-monospace text-secondary">Producto</th>
                <th class="font-monospace text-secondary">Precio Unitario</th>
                <th class="font-monospace text-secondary">Cantidad</th>
                <th class="font-monospace text-secondary">Subtotal</th>
                <th class="font-monospace text-secondary">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartData['items'] as $item): ?>
            <tr>
                <td class="align-middle"><strong>
                        <?= $item['name']?>
                    </strong></td>
                <td class="align-middle">$
                    <?= $item['price']?>
                </td>
                <td class="align-middle">
                    <form action="/carrito/actualizar" method="POST" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                        <input type="hidden" name="product_id" value="<?= $item['product_id']?>">
                        <input type="number" name="quantity" value="<?= $item['quantity']?>" min="1"
                            max="<?= $item['stock_quantity']?>"
                            class="form-control form-control-sm glass-input text-light text-center"
                            style="width: 60px; background: rgba(0,0,0,0.5);">
                        <button type="submit" class="btn btn-sm btn-hard"
                            title="Actualizar Cantidad">Actualizar</button>
                    </form>
                </td>
                <td class="align-middle fw-bold text-success font-monospace"
                    style="font-family: 'Michroma', sans-serif !important;">$
                    <?= $item['item_total']?>
                </td>
                <td class="align-middle text-end">
                    <form action="/carrito/eliminar" method="POST"
                        onsubmit="return confirm('¿Eliminar producto del carrito?');">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                        <input type="hidden" name="product_id" value="<?= $item['product_id']?>">
                        <button type="submit" class="btn btn-sm btn-hard btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php
    endforeach; ?>
        </tbody>
        <tfoot style="border-top: 2px solid #334455;">
            <tr>
                <td colspan="3" class="text-end font-monospace small">Total:</td>
                <td colspan="2" class="fw-bold text-success fs-5"
                    style="font-family: 'Michroma', sans-serif !important;">$
                    <?= $cartData['total']?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-4">
    <a href="/" class="btn btn-hard">Seguir Comprando</a>
    <form action="/carrito/checkout" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
        <button type="submit" class="btn btn-hard btn-primary btn-lg">Proceder al Pago</button>
    </form>
</div>
<?php
endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>