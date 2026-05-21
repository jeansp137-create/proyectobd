<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Portal Docente</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-container">
                <div class="logo-icon">BD</div>
                <span class="logo-text">Universidad de los Llanos</span>
            </div>
            <div>
                <span class="user-badge">Bases de Datos I</span>
            </div>
        </div>
    </header>

    <main class="app-container">
        <div class="login-wrapper">
            <div class="glass-card login-card">
                <h2>Portal Docente</h2>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Por favor, ingrese sus credenciales universitarias para gestionar las calificaciones.
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <span>⚠️</span>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form action="index.php?c=Auth&a=index" method="POST">
                    <div class="form-group">
                        <label for="cod_doc">Código del Docente</label>
                        <input type="text" id="cod_doc" name="cod_doc" placeholder="Ej. DOC-01" required autocomplete="username">
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="clave">Clave de Acceso</label>
                        <input type="password" id="clave" name="clave" placeholder="••••••••" required autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Ingresar al Sistema
                    </button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
