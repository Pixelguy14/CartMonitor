<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Catálogo de Productos</h2>
</div>

<!-- Barra de Filtros (Busqueda por rango y texto) -->
<div class="card shadow-sm mb-4">
    <div class="card-body bg-light">
        <form action="/" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label mb-0 fw-bold">Buscar por Nombre</label>
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Ej: Laptop..."
                        value="<?= $searchQuery ?? ''?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-0 fw-bold">Precio Mínimo ($)</label>
                <input type="number" name="min_price" class="form-control" placeholder="0.00"
                    value="<?= $searchMin ?? ''?>">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-0 fw-bold">Precio Máximo ($)</label>
                <input type="number" name="max_price" class="form-control" placeholder="0.00"
                    value="<?= $searchMax ?? ''?>">
            </div>
            <div class="col-md-2 d-flex align-items-end mt-4">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php if (empty($products)): ?>
    <div class="col-12">
        <div class="alert alert-info">No hay productos disponibles por el momento.</div>
    </div>
    <?php
else: ?>
    <?php foreach ($products as $p): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">
                    <?= $p['name']?>
                </h5>
                <p class="card-text text-muted">
                    <?= mb_strimwidth($p['description'], 0, 80, "...")?>
                </p>
                <h4 class="text-primary">$
                    <?= $p['price']?>
                </h4>
            </div>
            <div class="card-footer bg-white border-top-0">
                <a href="/producto/<?= $p['id']?>" class="btn btn-outline-primary w-100">Ver Detalles</a>
            </div>
        </div>
    </div>
    <?php
    endforeach; ?>
    <?php
endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/master.php';
?>