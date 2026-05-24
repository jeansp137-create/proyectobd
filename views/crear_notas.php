<?php
$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]." de ".date('Y');

// Cálculo de la suma total de porcentajes
$sumaActual = 0.0;
foreach ($cohortes as $ch) {
    $sumaActual += (float) $ch['porcentaje'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Cohortes - Registro de Notas</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <div class="header-top">
        REGISTRO DE NOTAS
        <span class="date-display"><?php echo htmlspecialchars($fecha_actual); ?></span>
    </div>
    <div class="header-bottom">
        CREAR NOTAS DE CURSO - <?= htmlspecialchars($curso['nomb_cur']) ?>
    </div>

    <main class="app-container">
        <!-- Estructura de Paneles 70/30 -->
        <div class="panel-container">
            <!-- Columna Izquierda (70%) -->
            <div class="panel-left">
                <h2 class="section-title">CONFIGURACIÓN DE COHORTES (NOTAS PARCIALES)</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($exito)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($exito) ?>
                    </div>
                <?php endif; ?>

                <!-- Resumen de porcentajes -->
                <div style="background-color: #fafafa; border: 1px solid #dddddd; padding: 15px; margin-bottom: 25px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: 700;">
                        <span>Porcentaje Evaluado Acumulado:</span>
                        <span style="color: <?= $sumaActual === 100.00 ? '#5cb85c' : ($sumaActual > 100.00 ? '#d9534f' : '#337ab7') ?>;">
                            <?= $sumaActual ?>% / 100%
                        </span>
                    </div>
                    
                    <!-- Barra de progreso rígida y simple -->
                    <div style="width: 100%; height: 10px; background-color: #e0e0e0; border: 1px solid #cccccc; overflow: hidden; margin-bottom: 8px;">
                        <div style="width: <?= min($sumaActual, 100) ?>%; height: 100%; background-color: <?= $sumaActual === 100.00 ? '#5cb85c' : ($sumaActual > 100.00 ? '#d9534f' : '#337ab7') ?>;"></div>
                    </div>

                    <?php if ($sumaActual < 100.00): ?>
                        <p style="color: #666666; font-size: 11px;">
                            💡 Falta configurar un <strong><?= 100.00 - $sumaActual ?>%</strong> para completar el plan académico.
                        </p>
                    <?php elseif ($sumaActual === 100.00): ?>
                        <p style="color: #3c763d; font-size: 11px; font-weight: 700;">
                            🎉 ¡Perfecto! El total de cohortes configurados cubre exactamente el 100% del curso.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Formulario de Adición o Edición -->
                <div style="border: 1px solid #cccccc; padding: 20px; background-color: #fcfcfc; margin-bottom: 30px;">
                    <h3 style="font-size: 14px; font-weight: 700; color: #333333; margin-bottom: 15px; text-transform: uppercase; border-bottom: 1px solid #eeeeee; padding-bottom: 5px;">
                        <?= $cohorteEditar ? 'Modificar Cohorte' : 'Adicionar Nota Parcial' ?>
                    </h3>
                    <form action="index.php?c=Nota&a=cohortes" method="POST">
                        <?php if ($cohorteEditar): ?>
                            <input type="hidden" name="nota_id" value="<?= htmlspecialchars($cohorteEditar['nota']) ?>">
                        <?php endif; ?>

                        <div class="form-group-row">
                            <label for="posicion">* Posición (Orden):</label>
                            <div class="input-wrapper">
                                <input type="number" id="posicion" name="posicion" min="1" required 
                                       value="<?= $cohorteEditar ? htmlspecialchars($cohorteEditar['posicion']) : (count($cohortes) + 1) ?>">
                            </div>
                        </div>

                        <div class="form-group-row">
                            <label for="descripcion">* Descripción:</label>
                            <div class="input-wrapper">
                                <input type="text" id="descripcion" name="descripcion" placeholder="Ej. Primer corte" required 
                                       value="<?= $cohorteEditar ? htmlspecialchars($cohorteEditar['desc_nota']) : '' ?>">
                            </div>
                        </div>

                        <div class="form-group-row">
                            <label for="porcentaje">* Porcentaje (%):</label>
                            <div class="input-wrapper">
                                <input type="number" id="porcentaje" name="porcentaje" min="1" max="100" step="0.01" required placeholder="Ej. 30" 
                                       value="<?= $cohorteEditar ? htmlspecialchars($cohorteEditar['porcentaje']) : '' ?>">
                            </div>
                        </div>

                        <div style="text-align: right; margin-top: 15px;">
                            <?php if ($cohorteEditar): ?>
                                <a href="index.php?c=Nota&a=cohortes" class="btn btn-secondary" style="margin-right: 5px;">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-success">+ Adicionar</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Cohortes -->
                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px; text-transform: uppercase;">Estructura Evaluativa Registrada</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 15%; text-align: center;">Posición</th>
                                <th style="width: 45%;">Descripción</th>
                                <th style="width: 15%;">Porcentaje</th>
                                <th style="width: 9%; text-align: center;">Editar</th>
                                <th style="width: 9%; text-align: center;">Borrar</th>
                                <th style="width: 12%; text-align: center;">Registrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cohortes)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #777777; padding: 20px;">Aún no ha configurado ninguna nota parcial o cohorte para este curso.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cohortes as $ch): ?>
                                    <tr>
                                        <td style="text-align: center;"><strong><?= htmlspecialchars($ch['posicion']) ?></strong></td>
                                        <td><?= htmlspecialchars($ch['desc_nota']) ?></td>
                                        <td><strong><?= htmlspecialchars($ch['porcentaje']) ?>%</strong></td>
                                        <td style="text-align: center;">
                                            <a href="index.php?c=Nota&a=cohortes&edit_nota=<?= urlencode($ch['nota']) ?>" class="icon-action icon-blue" title="Editar cohorte">✏️</a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="index.php?c=Nota&a=eliminar_cohorte&nota=<?= urlencode($ch['nota']) ?>" class="icon-action icon-red" onclick="return confirm('¿Está seguro de eliminar este cohorte? Esto borrará permanentemente todas las calificaciones de los estudiantes cargadas en él.');" title="Borrar cohorte">🗑️</a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="index.php?c=Nota&a=registro" class="icon-action icon-green" title="Digitar calificaciones de este cohorte">📝</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Columna Derecha (30%) -->
            <div class="panel-right">
                <h3 class="panel-title">OPCIONES</h3>
                
                <a href="index.php?c=Curso&a=estudiantes" class="btn btn-option">Volver a inscritos</a>
                <a href="index.php?c=Nota&a=registro" class="btn btn-option">Ver planilla</a>
                <a href="index.php?c=Auth&a=logout" class="btn btn-option" style="background-color: #555555; border-color: #444444;">Cerrar sesión</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
        <p style="font-size: 11px; color: #888888; margin-top: 5px;">Conexiones físicas a PostgreSQL en esta petición: <strong><?= Conexion::getConectarCount() ?></strong> (Singleton Activo)</p>
    </footer>
</body>
</html>
