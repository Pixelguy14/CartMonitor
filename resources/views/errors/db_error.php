<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sincronizando Sistema - CartMonitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f0f0f;
            --accent: #00ff88;
        }

        body {
            background-color: var(--bg-dark);
            color: white;
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(0, 255, 136, 0.1);
            border-top: 3px solid var(--accent);
            border-radius: 50%;
            display: inline-block;
            animation: spin 1s linear infinite;
            margin-bottom: 2rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        h1 {
            font-weight: 700;
            letter-spacing: -1px;
            color: var(--accent);
        }

        p {
            color: #aaaaaa;
            font-weight: 300;
        }

        .btn-retry {
            background: var(--accent);
            color: black;
            font-weight: 700;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            margin-top: 2rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-retry:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 255, 136, 0.3);
            color: black;
        }
    </style>
</head>

<body>
    <div class="glass-card">
        <div class="loader"></div>
        <h1>Sincronizando Motores</h1>
        <p>Estamos preparando la base de datos para tu sesión. Esto sucede normalmente tras un reinicio del contenedor
            (migraciones y semillas en proceso).</p>
        <p class="small text-muted">Intenta refrescar en unos segundos.</p>
        <a href="javascript:location.reload()" class="btn-retry">REINTENTAR CONEXIÓN</a>
    </div>
</body>

</html>