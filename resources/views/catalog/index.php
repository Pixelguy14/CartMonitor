<?php
$csrf_token = \App\Core\SessionManager::generateCsrfToken();
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-dark pb-2">
    <h2 class="label-sys fs-5 mb-0">Catálogo de Productos</h2>
</div>

<!-- Barra de Filtros (Busqueda por rango y texto) -->
<div class="glass-panel p-0 mb-4" style="background: var(--bg-white);">
    <div class="p-3 border-bottom border-dark bg-success text-dark">
        <span class="label-sys mb-0 text-white">Filtros de Búsqueda</span>
    </div>
    <div class="p-3">
        <form action="/" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label font-monospace small fw-bold">Buscar Producto</label>
                <div class="input-group">
                    <input type="text" name="q" class="form-control glass-input" placeholder="Ej: Laptop..."
                        value="<?= $searchQuery ?? ''?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label font-monospace small fw-bold">Precio Mín.</label>
                <input type="number" name="min_price" class="form-control glass-input" placeholder="0.00"
                    value="<?= $searchMin ?? ''?>">
            </div>
            <div class="col-md-3">
                <label class="form-label font-monospace small fw-bold">Precio Máx.</label>
                <input type="number" name="max_price" class="form-control glass-input" placeholder="0.00"
                    value="<?= $searchMax ?? ''?>">
            </div>
            <div class="col-md-2 mt-4 mt-md-0">
                <button type="submit" class="btn btn-hard btn-primary w-100">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php if (empty($products)): ?>
    <div class="col-12">
        <div class="alert alert-info rounded-0 border-dark">No se encontraron productos que coincidan con la búsqueda.
        </div>
    </div>
    <?php
else: ?>
    <?php foreach ($products as $p): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 glass-panel p-0" style="background: var(--bg-white);">
            <div style="border-bottom: 2px solid var(--border-color); overflow: hidden;">
                <?php if (!empty($p['image_url'])): ?>
                <img src="<?= $p['image_url']?>" class="card-img-top rounded-0" alt="<?= $p['name']?>"
                    style="height: 200px; object-fit: cover;">
                <?php
        else: ?>
                <div class="bg-dark text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                    <span class="font-monospace small">Sin Imagen</span>
                </div>
                <?php
        endif; ?>
            </div>
            <div class="p-3">
                <h5 class="font-monospace fw-bold m-0" style="text-transform: uppercase;">
                    <?= $p['name']?>
                </h5>
                <h4 class="text-primary mt-2" style="font-family: 'Michroma', sans-serif;">$
                    <?= $p['price']?>
                </h4>
            </div>
            <div class="p-3 border-top border-dark bg-light d-flex gap-2">
                <button onclick="toggleDetails(<?= $p['id']?>)" class="btn btn-hard flex-grow-1"
                    style="font-size: 0.7rem;">Ver Detalles</button>
            </div>

            <!-- Detalles en línea ocultos por defecto -->
            <div id="details-<?= $p['id']?>" class="p-3 border-top border-dark d-none"
                style="background: var(--ghost-ash);">
                <p class="font-monospace text-muted small mb-2">
                    <?= $p['description']?>
                </p>
                <p class="font-monospace small mb-3"><strong>Stock:</strong> <span class="badge bg-dark rounded-0">
                        <?= $p['stock_quantity']?>
                    </span></p>

                <?php if ($p['stock_quantity'] > 0): ?>
                <form action="/carrito/agregar" method="POST" class="d-flex flex-column gap-2">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token?>">
                    <input type="hidden" name="product_id" value="<?= $p['id']?>">

                    <label for="quantity-<?= $p['id']?>" class="font-monospace small fw-bold">Cantidad:</label>
                    <input type="number" id="quantity-<?= $p['id']?>" name="quantity"
                        class="form-control glass-input bg-white mb-2" value="1" min="1"
                        max="<?= $p['stock_quantity']?>" required>

                    <button type="submit" class="btn btn-hard btn-success w-100">Añadir al Carrito</button>
                </form>
                <?php
        else: ?>
                <div class="alert alert-warning rounded-0 border-dark p-2 text-center font-monospace small">Agotado
                </div>
                <?php
        endif; ?>
            </div>
        </div>
    </div>
    <?php
    endforeach; ?>
    <?php
endif; ?>
</div>

<script>
    function toggleDetails(id) {
        const el = document.getElementById('details-' + id);
        if (el.classList.contains('d-none')) {
            el.classList.remove('d-none');
        } else {
            el.classList.add('d-none');
        }
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>