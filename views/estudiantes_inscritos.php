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
    <title>Estudiantes Inscritos - Registro de Notas</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <div class="header-top">
        REGISTRO DE NOTAS
        <span class="date-display"><?php echo htmlspecialchars($fecha_actual); ?></span>
    </div>
    <div class="header-bottom">
        CURSO: <?= htmlspecialchars($curso['nomb_cur']) ?> (<?= htmlspecialchars($curso['cod_cur']) ?>)
    </div>

    <main class="app-container">
        <!-- Estructura de Paneles 70/30 -->
        <div class="panel-container">
            <!-- Columna Izquierda (70%) -->
            <div class="panel-left">
                <h2 class="section-title">ESTUDIANTES MATRICULADOS</h2>
                
                <?php if (isset($_SESSION['exito_mensaje'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['exito_mensaje']) ?>
                    </div>
                    <?php unset($_SESSION['exito_mensaje']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_mensaje'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['error_mensaje']) ?>
                    </div>
                    <?php unset($_SESSION['error_mensaje']); ?>
                <?php endif; ?>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 10%; text-align: center;">No.</th>
                                <th style="width: 25%;">Código</th>
                                <th style="width: 50%;">Nombres y Apellidos</th>
                                <th style="width: 15%; text-align: center;">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($estudiantesInscritos)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #777777; padding: 20px;">No hay estudiantes inscritos en este curso para este año/periodo.</td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $contador = 1; 
                                foreach ($estudiantesInscritos as $est): 
                                ?>
                                    <tr>
                                        <td style="text-align: center;"><?= $contador++ ?></td>
                                        <td><strong><?= htmlspecialchars($est['cod_est']) ?></strong></td>
                                        <td><?= htmlspecialchars($est['nomb_est']) ?></td>
                                        <td style="text-align: center;">
                                            <a href="index.php?c=Curso&a=desinscribir&cod_est=<?= urlencode($est['cod_est']) ?>" 
                                               class="icon-action icon-red" 
                                               onclick="return confirm('¿Está seguro de que desea eliminar la inscripción de este estudiante? Se eliminarán todas sus calificaciones asociadas en cascada.');"
                                               title="Eliminar Inscripción">
                                                🗑️
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Formulario de inscripción individual (Formulario clásico con etiquetas y select) -->
                <div style="margin-top: 30px; padding: 20px; border: 1px solid #dddddd; background-color: #fbfbfb;">
                    <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px; text-transform: uppercase;">Matricular Estudiante Individualmente</h3>
                    <form action="index.php?c=Curso&a=inscribir" method="POST">
                        <div class="form-group-row" style="margin-bottom: 15px;">
                            <label for="cod_est">* Seleccionar Estudiante:</label>
                            <div class="input-wrapper">
                                <select id="cod_est" name="cod_est" required>
                                    <option value="">-- Elija un Estudiante --</option>
                                    <?php foreach ($todosEstudiantes as $est): ?>
                                        <option value="<?= htmlspecialchars($est['cod_est']) ?>">
                                            [<?= htmlspecialchars($est['cod_est']) ?>] <?= htmlspecialchars($est['nomb_est']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-success" style="width: 100px;">Inscribir</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Columna Derecha (30%) -->
            <div class="panel-right">
                <h3 class="panel-title">OPCIONES DE CURSO</h3>
                
                <a href="index.php?c=Nota&a=cohortes" class="btn btn-outline-success">Crear notas de curso</a>
                <a href="index.php?c=Curso&a=cargar_estudiantes_vista" class="btn btn-outline-success">Cargar estudiantes</a>
                <a href="index.php?c=Nota&a=registro" class="btn btn-outline-success">Ver planilla</a>
                
                <a href="index.php?c=Curso&a=index" class="btn btn-option" style="margin-top: 15px; background-color: #555555; border-color: #444444;">Cambiar Curso</a>
                
                <div class="recuadro-amarillo">
                    <strong>Nota Académica:</strong><br>
                    Este módulo le permite matricular alumnos de manera individual seleccionándolos de la base de datos general, o de manera masiva utilizando un archivo en formato CSV. Asegúrese de que el estudiante esté registrado en el sistema antes de proceder con su matriculación individual.
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
