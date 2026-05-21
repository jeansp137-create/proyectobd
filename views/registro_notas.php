<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Calificaciones - Portal Docente</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-container">
                <div class="logo-icon">BD</div>
                <span class="logo-text">Universidad de los Llanos</span>
            </div>
            <div class="user-info">
                <span class="user-badge">👤 Prof. <?= htmlspecialchars($_SESSION['docente_nombre']) ?></span>
                <a href="index.php?c=Auth&a=logout" class="btn btn-secondary" style="padding: 0.35rem 0.85rem; font-size: 0.875rem;">Salir</a>
            </div>
        </div>
    </header>

    <main class="app-container">
        <!-- Información del Curso Activo -->
        <div class="info-bar">
            <div class="info-item">
                <span>Curso Activo</span>
                <h4><?= htmlspecialchars($curso['nomb_cur']) ?> (<?= htmlspecialchars($curso['cod_cur']) ?>)</h4>
            </div>
            <div class="info-item">
                <span>Año Académico</span>
                <h4><?= htmlspecialchars($_SESSION['year_activo']) ?></h4>
            </div>
            <div class="info-item">
                <span>Periodo Semestral</span>
                <h4><?= htmlspecialchars($_SESSION['periodo_activo']) ?></h4>
            </div>
            <div class="info-item" style="display: flex; align-items: center; justify-content: center;">
                <a href="index.php?c=Curso&a=index" class="btn btn-secondary" style="width: 100%;">🔄 Cambiar Selección</a>
            </div>
        </div>

        <!-- Pestañas de Navegación -->
        <div class="nav-tabs">
            <a href="index.php?c=Curso&a=estudiantes" class="nav-link">👥 Estudiantes Inscritos</a>
            <a href="index.php?c=Nota&a=cohortes" class="nav-link">⚙️ Configurar Cohortes</a>
            <a href="index.php?c=Nota&a=registro" class="nav-link active">📝 Registrar Calificaciones</a>
        </div>

        <!-- Alertas de Negocio -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <div><?= htmlspecialchars($exito) ?></div>
            </div>
        <?php endif; ?>

        <div class="glass-card" style="padding: 2.5rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2>Planilla de Calificaciones</h2>
                    <p style="color: var(--text-secondary);">
                        Digite las notas de los alumnos en cada cohorte. Los campos vacíos no serán guardados.
                    </p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <!-- Botón Generar PDF -->
                    <a href="index.php?c=Nota&a=pdf" target="_blank" class="btn btn-secondary">
                        🖨️ Generar PDF / Imprimir
                    </a>
                </div>
            </div>

            <?php if (empty($cohortes)): ?>
                <div style="text-align: center; padding: 4rem 1rem; color: var(--text-secondary); border: 1px dashed var(--border-color); border-radius: 12px;">
                    <span style="font-size: 3rem; display: block; margin-bottom: 1rem;">⚙️</span>
                    No puede registrar notas porque no ha configurado ningún cohorte. 
                    <br>
                    <a href="index.php?c=Nota&a=cohortes" class="btn btn-primary" style="margin-top: 1.5rem;">Configurar Cohortes Ahora</a>
                </div>
            <?php elseif (empty($estudiantes)): ?>
                <div style="text-align: center; padding: 4rem 1rem; color: var(--text-secondary); border: 1px dashed var(--border-color); border-radius: 12px;">
                    <span style="font-size: 3rem; display: block; margin-bottom: 1rem;">👥</span>
                    No hay estudiantes matriculados en este curso para registrar calificaciones.
                    <br>
                    <a href="index.php?c=Curso&a=estudiantes" class="btn btn-primary" style="margin-top: 1.5rem;">Inscribir Estudiantes Ahora</a>
                </div>
            <?php else: ?>
                <form action="index.php?c=Nota&a=registro" method="POST">
                    <div class="table-container" style="margin-top: 0; margin-bottom: 1.5rem;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No.</th>
                                    <th style="width: 100px;">Código</th>
                                    <th>Nombres y Apellidos</th>
                                    
                                    <!-- Columnas de Cohortes creados dinámicamente -->
                                    <?php foreach ($cohortes as $coh): ?>
                                        <th style="text-align: center; width: 140px;">
                                            <?= htmlspecialchars($coh['desc_nota']) ?>
                                            <span style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: normal; margin-top: 0.25rem;">
                                                (<?= htmlspecialchars($coh['porcentaje']) ?>%)
                                            </span>
                                        </th>
                                    <?php endforeach; ?>

                                    <!-- Columna Definitiva Calculada -->
                                    <th style="text-align: center; width: 130px; background-color: rgba(99, 102, 241, 0.15);">
                                        Definitiva
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
                                        <td><?= $contador++ ?></td>
                                        <td style="font-weight: 600; color: var(--accent-blue);"><?= htmlspecialchars($est['cod_est']) ?></td>
                                        <td style="font-weight: 500;"><?= htmlspecialchars($est['nomb_est']) ?></td>

                                        <!-- Celdas de Notas Numéricas -->
                                        <?php foreach ($cohortes as $coh): 
                                            $nota_id = $coh['nota'];
                                            $val_nota = isset($calificaciones[$est['cod_est']][$nota_id]) ? $calificaciones[$est['cod_est']][$nota_id] : '';
                                            
                                            // Sumamos al promedio ponderado si tiene calificación guardada
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
                                                       value="<?= $val_nota ?>"
                                                       autocomplete="off">
                                            </td>
                                        <?php endforeach; ?>

                                        <!-- Celda de Definitiva Ponderada -->
                                        <td style="text-align: center; background-color: rgba(99, 102, 241, 0.05); font-weight: 700;">
                                            <?php if ($sumaPorcentajes > 0): ?>
                                                <!-- Escalamos la definitiva en base al porcentaje cargado si aún no está al 100% -->
                                                <?php 
                                                // Si el curso tiene cohortes que no suman 100% pero queremos mostrar el acumulado real
                                                // mostramos la ponderación actual.
                                                $def_mostrar = number_format($definitiva, 2);
                                                $clase_badge = $definitiva >= 3.0 ? 'grade-pass' : 'grade-fail';
                                                ?>
                                                <span class="grade-badge <?= $clase_badge ?>"><?= $def_mostrar ?></span>
                                            <?php else: ?>
                                                <span style="color: var(--text-secondary); font-size: 0.85rem; font-weight: normal;">Sin notas</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Botón para Guardar Notas -->
                    <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="submit" class="btn btn-success" style="padding: 0.85rem 2.5rem;">
                            💾 Guardar Planilla de Notas
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
