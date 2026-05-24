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
    <title>Registrar Calificaciones - Registro de Notas</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <style>
        .input-grade {
            width: 70px;
            padding: 5px;
            text-align: center;
            border: 1px solid #cccccc;
            font-size: 13px;
        }
        .input-grade:focus {
            outline: none;
            border-color: #337ab7;
        }
        .definitiva-value {
            font-size: 14px;
            font-weight: 700;
        }
        .definitiva-pass {
            color: #3c763d;
        }
        .definitiva-fail {
            color: #a94442;
        }
    </style>
</head>
<body>
    <div class="header-top">
        REGISTRO DE NOTAS
        <span class="date-display"><?php echo htmlspecialchars($fecha_actual); ?></span>
    </div>
    <div class="header-bottom">
        REGISTRO DE CALIFICACIONES - <?= htmlspecialchars($curso['nomb_cur']) ?>
    </div>

    <main class="app-container">
        <!-- Estructura de Paneles 70/30 -->
        <div class="panel-container">
            <!-- Columna Izquierda (70%) -->
            <div class="panel-left">
                <h2 class="section-title">PLANILLA DE CALIFICACIONES</h2>

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

                <?php if (empty($cohortes)): ?>
                    <div style="text-align: center; padding: 40px 20px; color: #777777; border: 1px dashed #cccccc; background-color: #fafafa;">
                        ⚠️ No puede registrar notas porque no ha configurado ningún cohorte para este curso.<br><br>
                        <a href="index.php?c=Nota&a=cohortes" class="btn btn-success">+ Configurar Cohortes</a>
                    </div>
                <?php elseif (empty($estudiantes)): ?>
                    <div style="text-align: center; padding: 40px 20px; color: #777777; border: 1px dashed #cccccc; background-color: #fafafa;">
                        ⚠️ No hay estudiantes inscritos en este curso para registrar calificaciones.<br><br>
                        <a href="index.php?c=Curso&a=estudiantes" class="btn btn-success">Matricular Estudiantes</a>
                    </div>
                <?php else: ?>
                    <p style="color: #666666; margin-bottom: 20px; font-size: 12px;">
                        Digite las notas de los alumnos en cada cohorte (Rango: 0.0 - 5.0). Los campos que deje vacíos no se guardarán ni afectarán el acumulado.
                    </p>

                    <form action="index.php?c=Nota&a=registro" method="POST">
                        <div class="table-responsive">
                            <table class="planilla-calificaciones">
                                <thead>
                                    <tr>
                                        <th style="width: 8%; text-align: center; background-color: #ffeb3b !important; color: #000000 !important; border: 1px solid #dcd123;">No.</th>
                                        <th style="width: 15%; background-color: #ffeb3b !important; color: #000000 !important; border: 1px solid #dcd123;">Código</th>
                                        <th style="width: 37%; background-color: #ffeb3b !important; color: #000000 !important; border: 1px solid #dcd123;">Nombres y Apellidos</th>
                                        
                                        <!-- Columnas de Cohortes creados dinámicamente -->
                                        <?php foreach ($cohortes as $coh): ?>
                                            <th style="text-align: center; width: 13%; background-color: #ffeb3b !important; color: #000000 !important; border: 1px solid #dcd123;">
                                                <?= htmlspecialchars($coh['desc_nota']) ?>
                                                <span style="display: block; font-size: 10px; font-weight: normal; margin-top: 2px;">
                                                    (<?= htmlspecialchars($coh['porcentaje']) ?>%)
                                                </span>
                                            </th>
                                        <?php endforeach; ?>

                                        <!-- Columna Definitiva Calculada -->
                                        <th style="text-align: center; width: 14%; background-color: #ffeb3b !important; color: #000000 !important; border: 1px solid #dcd123;">
                                            Definitiva
                                            <span style="display: block; font-size: 10px; font-weight: normal; margin-top: 2px;">
                                                (100%)
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador = 1;
                                    foreach ($estudiantes as $est): 
                                        $definitiva = 0.0;
                                        $sumaPorcentajes = 0.0;
                                    ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $contador++ ?></td>
                                            <td><strong><?= htmlspecialchars($est['cod_est']) ?></strong></td>
                                            <td><?= htmlspecialchars($est['nomb_est']) ?></td>

                                            <!-- Celdas de Notas Numéricas -->
                                            <?php foreach ($cohortes as $coh): 
                                                $nota_id = $coh['nota'];
                                                $val_nota = isset($calificaciones[$est['cod_est']][$nota_id]) ? $calificaciones[$est['cod_est']][$nota_id] : '';
                                                
                                                if ($val_nota !== '') {
                                                    $definitiva += (float) $val_nota * ((float) $coh['porcentaje'] / 100.0);
                                                    $sumaPorcentajes += (float) $coh['porcentaje'];
                                                }
                                            ?>
                                                <td style="text-align: center;">
                                                    <input type="number" 
                                                           name="notas[<?= htmlspecialchars($est['cod_est']) ?>][<?= htmlspecialchars($nota_id) ?>]" 
                                                           class="input-grade" 
                                                           min="0" 
                                                           max="5" 
                                                           step="0.1" 
                                                           placeholder="0.0" 
                                                           value="<?= $val_nota !== '' ? number_format($val_nota, 1) : '' ?>"
                                                           autocomplete="off">
                                                </td>
                                            <?php endforeach; ?>

                                            <!-- Celda de Definitiva Ponderada -->
                                            <td style="text-align: center;" class="definitiva-cell">
                                                <?php if ($sumaPorcentajes > 0): ?>
                                                    <?php 
                                                    $def_mostrar = number_format($definitiva, 2);
                                                    $clase_def = $definitiva >= 3.0 ? 'definitiva-pass' : 'definitiva-fail';
                                                    ?>
                                                    <span class="definitiva-value <?= $clase_def ?>"><?= $def_mostrar ?></span>
                                                <?php else: ?>
                                                    <span style="color: #888888; font-size: 11px;">Sin notas</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón para Guardar Notas -->
                        <div style="margin-top: 20px; text-align: right;">
                            <button type="submit" class="btn btn-success" style="padding: 10px 20px;">
                                💾 Guardar Planilla de Notas
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Columna Derecha (30%) -->
            <div class="panel-right">
                <h3 class="panel-title">OPCIONES</h3>
                
                <a href="index.php?c=Curso&a=estudiantes" class="btn btn-option">Volver a inscritos</a>
                <a href="index.php?c=Nota&a=cohortes" class="btn btn-option">Configurar cohortes</a>
                <a href="index.php?c=Nota&a=pdf" target="_blank" class="btn btn-option" style="background-color: #3f863f; border-color: #357035;">Generar PDF / Imprimir</a>
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
