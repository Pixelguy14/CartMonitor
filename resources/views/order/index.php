<?php ob_start(); ?>

<h2 class="mb-4">Historial de Compras</h2>

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

<?php if (empty($orders)): ?>
<div class="alert alert-info">Aún no has realizado ninguna compra. <a href="/" class="alert-link">Ver el catálogo</a>
</div>
<?php
else: ?>
<div class="row">
    <?php foreach ($orders as $order): ?>
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold">Orden #
                    <?= str_pad($order['id'], 6, '0', STR_PAD_LEFT)?>
                </span>
                <span class="text-muted"><small>Realizada el:
                        <?= date('d/m/Y H:i', strtotime($order['created_at']))?>
                    </small></span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-success fs-6">Pagado (Completada)</span>
                    <h4 class="mb-0 text-success fw-bold">Total: $
                        <?= number_format((float)$order['total_amount'], 2)?>
                    </h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-borderless align-middle mb-0">
                        <thead class="text-muted border-bottom">
                            <tr>
                                <th>Producto</th>
                                <th>Precio (al comprar)</th>
                                <th>Cantidad</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <?= $item['name']?>
                                </td>
                                <td>$
                                    <?= number_format((float)$item['price_at_purchase'], 2)?>
                                </td>
                                <td>x
                                    <?= $item['quantity']?>
                                </td>
                                <td class="text-end fw-bold">$
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