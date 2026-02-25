<?php ob_start(); ?>

<h2 class="mb-4">Tu Carrito de Compras</h2>

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

<?php if (empty($cartData['items'])): ?>
<div class="alert alert-info">Tu carrito está vacío. <a href="/" class="alert-link">Explorar catálogo</a></div>
<?php
else: ?>
<div class="table-responsive">
    <table class="table table-hover bg-white shadow-sm rounded">
        <thead class="table-light">
            <tr>
                <th>Producto</th>
                <th>Precio Unit.</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th></th>
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
                            max="<?= $item['stock_quantity']?>" class="form-control form-control-sm"
                            style="width: 80px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary"
                            title="Actualizar Cantidad">↻</button>
                    </form>
                </td>
                <td class="align-middle fw-bold text-success">$
                    <?= $item['item_total']?>
                </td>
                <td class="align-middle text-end">
                    <form action="/carrito/eliminar" method="POST" onsubmit="return confirm('¿Quitar del carrito?');">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                        <input type="hidden" name="product_id" value="<?= $item['product_id']?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php
    endforeach; ?>
        </tbody>
        <tfoot class="border-top-2">
            <tr>
                <td colspan="3" class="text-end fw-bold">Total a Pagar:</td>
                <td colspan="2" class="fw-bold text-success fs-5">$
                    <?= $cartData['total']?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-4">
    <a href="/" class="btn btn-outline-secondary">Continuar Comprando</a>
    <form action="/carrito/checkout" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
        <button type="submit" class="btn btn-primary btn-lg">Proceder al Pago Seguro</button>
    </form>
</div>
<?php
endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>