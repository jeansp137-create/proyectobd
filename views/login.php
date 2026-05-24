<?php
$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]." de ".date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Registro de Notas</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <div class="header-top">
        REGISTRO DE NOTAS
        <span class="date-display"><?php echo htmlspecialchars($fecha_actual); ?></span>
    </div>
    <div class="header-bottom">
        INICIO DE SESION
    </div>

    <main class="app-container" style="display: flex; align-items: center; justify-content: center; min-height: 60vh;">
        <div class="login-center-container">
            <h2 class="section-title">ACCESO DOCENTES</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?c=Auth&a=index" method="POST">
                <div class="form-group-row">
                    <label for="cod_doc">* Usuario:</label>
                    <div class="input-wrapper">
                        <input type="text" id="cod_doc" name="cod_doc" placeholder="Ej. DOC-01" required autocomplete="username">
                    </div>
                </div>

                <div class="form-group-row">
                    <label for="clave">* Clave:</label>
                    <div class="input-wrapper">
                        <input type="password" id="clave" name="clave" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                </div>

                <div style="margin-top: 25px; text-align: right;">
                    <button type="submit" class="btn btn-purple" style="width: 100px;">Aceptar</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
        <p style="font-size: 11px; color: #888888; margin-top: 5px;">Conexiones físicas a PostgreSQL en esta petición: <strong><?= Conexion::getConectarCount() ?></strong> (Singleton Activo)</p>
    </footer>
</body>
</html>
