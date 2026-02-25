<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CartMonitor Engine</title>
    <!-- Basic Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">CartMonitor</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Global Search Bar -->
                    <li class="nav-item">
                        <form action="/" method="GET" class="d-flex ms-lg-3">
                            <input class="form-control form-control-sm me-2" type="search"
                                placeholder="Buscar producto..." name="q" value="<?= $searchQuery ?? ''?>">
                            <button class="btn btn-sm btn-outline-light" type="submit">Buscar</button>
                        </form>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/">Catálogo</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-info" href="/admin/usuarios">Usuarios</a>
                    </li>
                    <?php
    endif; ?>

                    <!-- Menú dinámico para Proveedores y Admins -->
                    <?php if (isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ['proveedor', 'admin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link text-success" href="/proveedor/productos">Mis Productos</a>
                    </li>
                    <?php
    endif; ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="/carrito">Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-primary" href="/mis-ordenes">Mis Órdenes</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link text-light">Hola,
                            <?= htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8')?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/logout">Salir</a>
                    </li>
                    <?php
else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Ingresar</a>
                    </li>
                    <?php
endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Render specific view content here -->
        <?= $content ?? ''?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>