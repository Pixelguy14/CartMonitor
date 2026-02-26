<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CartMonitor Engine</title>
    <!-- Basic Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Maximalist Custom CSS -->
    <link href="/css/maximalist.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
</head>

<body>

    <!-- Parallax Backdrop Layer -->
    <div class="parallax-container" id="parallax"
        style="position: fixed; width: 110%; height: 110%; top: -5%; left: -5%; z-index: -1; display: grid; grid-template-columns: 1fr 1fr 1fr; filter: saturate(1.2); transition: transform 0.1s ease-out;">
        <div style="height: 100%; opacity: 0.9; background: #2E5BFF;"></div>
        <div style="height: 100%; opacity: 0.9; background: #FFD700;"></div>
        <div style="height: 100%; opacity: 0.9; background: #CCFF00;"></div>
    </div>

    <div class="ui-wrapper" style="min-height: 100vh; backdrop-filter: grayscale(0.2);">
        <nav class="navbar navbar-expand-lg navbar-maximalist mb-4">
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
                                <input class="form-control form-control-sm me-2 glass-input" type="search"
                                    placeholder="BUSCAR PRODUCTO..." name="q" value="<?= $searchQuery ?? ''?>"
                                    style="border-radius:0;">
                                <button class="btn btn-sm btn-hard" type="submit">BUSCAR</button>
                            </form>
                        </li>
                        <!--
                    <li class="nav-item">
                        <a class="nav-link" href="/">Catálogo</a>
                    </li>
                    -->
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-info font-monospace small fw-bold"
                                href="/admin/usuarios">Usuarios</a>
                        </li>
                        <?php
    endif; ?>

                        <!-- Menú dinámico para Proveedores y Admins -->
                        <?php if (isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ['proveedor', 'admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-success font-monospace small fw-bold"
                                href="/proveedor/productos">Mis Productos</a>
                        </li>
                        <?php
    endif; ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning font-monospace small fw-bold" href="/carrito">Carrito</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary font-monospace small fw-bold" href="/mis-ordenes">Mis
                                Órdenes</a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link text-light font-monospace small fw-bold">Hola,
                                <?= htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8')?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-info font-monospace small fw-bold" href="/perfil">Mi Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger font-monospace small fw-bold" href="/logout">Cerrar
                                Sesión</a>
                        </li>
                        <?php
else: ?>
                        <li class="nav-item">
                            <a class="nav-link font-monospace small fw-bold" href="/login">Iniciar Sesión</a>
                        </li>
                        <?php
endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container main-console"
            style="background: var(--glass-light); padding: 30px; border: 2px solid black; box-shadow: 4px 4px 0px rgba(0,0,0,1);">
            <!-- Render specific view content here -->
            <?= $content ?? ''?>
        </div>
    </div> <!-- end ui-wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple parallax logic for aesthetic
        document.addEventListener('mousemove', (e) => {
            const parallax = document.getElementById('parallax');
            const moveX = (e.clientX - window.innerWidth / 2) * 0.01;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.01;
            parallax.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    </script>
</body>

</html>