<?php
$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]." de ".date('Y');

// Variables para el control de periodos
$mes_actual  = (int) date('m');
$anio_actual = (int) date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Curso - Registro de Notas</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <div class="header-top">
        REGISTRO DE NOTAS
        <span class="date-display"><?php echo htmlspecialchars($fecha_actual); ?></span>
    </div>
    <div class="header-bottom">
        INFORMACION DE DOCENTES
    </div>

    <main class="app-container">
        <div class="panel-container">
            <div class="panel-left">
                <h2 class="section-title">SELECCIÓN DE CURSO ACADÉMICO</h2>
                <p style="color: #666666; margin-bottom: 25px; font-size: 13px;">
                    Seleccione el curso a su cargo, el año respectivo y el periodo para comenzar a gestionar los estudiantes y calificaciones.
                </p>

                <?php if (!empty($_SESSION['error_mensaje'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['error_mensaje']) ?>
                    </div>
                    <?php unset($_SESSION['error_mensaje']); ?>
                <?php endif; ?>

                <form action="index.php?c=Curso&a=estudiantes" method="POST">
                    <!-- Selección del Curso -->
                    <div class="form-group-row">
                        <label for="cod_cur">* Curso a su Cargo:</label>
                        <div class="input-wrapper">
                            <select id="cod_cur" name="cod_cur" required>
                                <option value="">-- Seleccione un Curso --</option>
                                <?php foreach ($cursos as $c): ?>
                                    <option value="<?= htmlspecialchars($c['cod_cur']) ?>">
                                        <?= htmlspecialchars($c['cod_cur']) ?> - <?= htmlspecialchars($c['nomb_cur']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Año Académico -->
                    <div class="form-group-row">
                        <label for="year">* Año Académico:</label>
                        <div class="input-wrapper">
                            <input type="number" id="year" name="year"
                                   value="<?= $anio_actual ?>"
                                   required
                                   min="2000"
                                   max="<?= $anio_actual ?>">
                        </div>
                    </div>

                    <!-- Periodo Académico -->
                    <div class="form-group-row">
                        <label>* Periodo Semestral:</label>
                        <div class="input-wrapper">
                            <div class="radio-group-horizontal">
                                <label>
                                    <input type="radio" name="periodo" value="Periodo I" checked>
                                    <span>Periodo I</span>
                                </label>
                                <label id="label-periodo2">
                                    <input type="radio" name="periodo" value="Periodo II" id="radio-periodo2">
                                    <span>Periodo II</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 30px; text-align: right;">
                        <button type="submit" class="btn btn-primary" style="width: 120px;">Aceptar</button>
                    </div>
                </form>
            </div>

            <div class="panel-right">
                <h3 class="panel-title">OPCIONES</h3>
                <a href="index.php?c=Curso&a=listar_cursos" class="btn btn-option">Adicionar cursos</a>
                <a href="index.php?c=Auth&a=logout" class="btn btn-option" style="background-color: #555555; border-color: #444444;">Cerrar sesión</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
        <p style="font-size: 11px; color: #888888; margin-top: 5px;">Conexiones físicas a PostgreSQL en esta petición: <strong><?= Conexion::getConectarCount() ?></strong> (Singleton Activo)</p>
    </footer>

    <script>
        const anioActual = <?= $anio_actual ?>;
        const mesActual  = <?= $mes_actual ?>;
        const inputAnio  = document.getElementById('year');
        const labelP2    = document.getElementById('label-periodo2');

        function actualizarPeriodos() {
            const anioSeleccionado = parseInt(inputAnio.value);

            if (anioSeleccionado < anioActual || (anioSeleccionado === anioActual && mesActual >= 7)) {
                labelP2.style.display = 'inline-flex';
            } else {
                labelP2.style.display = 'none';
                document.getElementById('radio-periodo2').checked = false;
                document.querySelector('input[value="Periodo I"]').checked = true;
            }
        }

        actualizarPeriodos();
        inputAnio.addEventListener('input', actualizarPeriodos);
    </script>
</body>
</html>