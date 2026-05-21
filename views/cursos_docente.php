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
        <!-- Contenedor con estructura 70/30 -->
        <div class="panel-container">
            <!-- Columna Izquierda (70%) -->
            <div class="panel-left">
                <h2 class="section-title">SELECCIÓN DE CURSO ACADÉMICO</h2>
                <p style="color: #666666; margin-bottom: 25px; font-size: 13px;">
                    Seleccione el curso a su cargo, el año respectivo y el periodo para comenzar a gestionar los estudiantes y calificaciones.
                </p>

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
                            <input type="number" id="year" name="year" placeholder="Ej. 2022" value="2022" required min="1900" max="2100">
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
                                <label>
                                    <input type="radio" name="periodo" value="Periodo II">
                                    <span>Periodo II</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <div style="margin-top: 30px; text-align: right;">
                        <button type="submit" class="btn btn-primary" style="width: 120px;">Aceptar</button>
                    </div>
                </form>
            </div>

            <!-- Columna Derecha (30%) -->
            <div class="panel-right">
                <h3 class="panel-title">OPCIONES</h3>
                
                <a href="index.php?c=Curso&a=listar_cursos" class="btn btn-option">Adicionar cursos</a>
                <a href="index.php?c=Auth&a=logout" class="btn btn-option" style="background-color: #555555; border-color: #444444;">Cerrar sesión</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
