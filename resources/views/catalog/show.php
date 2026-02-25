<?php
// Generamos Token CSRF
$csrf_token = \App\Core\SessionManager::generateCsrfToken();
ob_start();
?>

<div class="row">
    <div class="col-md-8">
        <h2>
            <?= $p['name']?>
        </h2>
        <p class="lead text-muted">
            <?= $p['description']?>
        </p>
        <h3 class="text-primary mt-4">$
            <?= $p['price']?>
        </h3>
        <p class="mt-3">Disponibles: <span class="badge bg-secondary">
                <?= $p['stock_quantity']?>
            </span></p>

        <?php if ($p['stock_quantity'] > 0): ?>
        <form action="/carrito/agregar" method="POST" class="mt-4 p-3 bg-white border rounded">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
            <input type="hidden" name="product_id" value="<?= $p['id']?>">

            <div class="row align-items-center">
                <div class="col-auto">
                    <label for="quantity" class="col-form-label">Cantidad:</label>
                </div>
                <div class="col-auto">
                    <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1"
                        max="<?= $p['stock_quantity']?>" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">Añadir al Carrito</button>
                </div>
            </div>
        </form>
        <?php
else: ?>
        <div class="alert alert-warning mt-4">Producto agotado.</div>
        <?php
endif; ?>

        <a href="/" class="btn btn-link mt-3">&larr; Volver al Catálogo</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>