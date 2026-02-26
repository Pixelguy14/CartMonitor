<?php ob_start(); ?>

<h2 class="label-sys fs-5 mb-4 border-bottom border-dark pb-2">Historial de Compras</h2>

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

<?php if (empty($orders)): ?>
<div class="alert alert-info rounded-0 border-dark">Aún no has realizado ninguna compra. <a href="/"
        class="alert-link">Ver Catálogo</a>
</div>
<?php
else: ?>
<div class="row">
    <?php foreach ($orders as $order): ?>
    <div class="col-12 mb-4">
        <div class="glass-panel p-0" style="background: var(--bg-white);">
            <div class="bg-dark text-white p-2 d-flex justify-content-between align-items-center">
                <span class="label-sys mb-0 border-0">Orden #
                    <?= str_pad($order['id'], 6, '0', STR_PAD_LEFT)?>
                </span>
                <span class="font-monospace small">Fecha:
                    <?= date('d/m/Y H:i', strtotime($order['created_at']))?>
                </span>
            </div>
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-success rounded-0 border border-dark text-dark font-monospace">Estado:
                        Pagado</span>
                    <h4 class="mb-0 text-success" style="font-family: 'Michroma', sans-serif;">$
                        <?= number_format((float)$order['total_amount'], 2)?>
                    </h4>
                </div>

                <div class="table-responsive glass-panel-dark p-0">
                    <table class="table table-dark table-sm align-middle mb-0" style="background: transparent;">
                        <thead style="border-bottom: 2px solid #334455;">
                            <tr>
                                <th class="font-monospace text-secondary">Producto</th>
                                <th class="font-monospace text-secondary">Precio de Compra</th>
                                <th class="font-monospace text-secondary">Cantidad</th>
                                <th class="text-end font-monospace text-secondary">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="font-monospace">
                                    <?= $item['name']?>
                                </td>
                                <td class="font-monospace">$
                                    <?= number_format((float)$item['price_at_purchase'], 2)?>
                                </td>
                                <td class="font-monospace">x
                                    <?= $item['quantity']?>
                                </td>
                                <td class="text-end text-success font-monospace">$
                                    <?= number_format(((float)$item['price_at_purchase'] * (int)$item['quantity']), 2)?>
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
    <?php
    endforeach; ?>
</div>
<?php
endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>