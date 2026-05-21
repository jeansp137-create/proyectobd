<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Cohortes - Portal Docente</title>
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
            <a href="index.php?c=Nota&a=cohortes" class="nav-link active">⚙️ Configurar Cohortes</a>
            <a href="index.php?c=Nota&a=registro" class="nav-link">📝 Registrar Calificaciones</a>
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

        <!-- Visualización del Porcentaje Total Configurado -->
        <?php 
        $sumaActual = 0.0;
        foreach ($cohortes as $ch) {
            $sumaActual += (float) $ch['porcentaje'];
        }
        ?>
        <div class="glass-card" style="padding: 1.5rem 2rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 600;">Suma Total de Porcentajes del Curso</h3>
                <span style="font-weight: 700; color: <?= $sumaActual === 100.00 ? 'var(--accent-green)' : ($sumaActual > 100.00 ? 'var(--accent-red)' : 'var(--accent-blue)') ?>;">
                    <?= $sumaActual ?>% / 100%
                </span>
            </div>
            <div class="progress-container" style="margin: 0;">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: <?= min($sumaActual, 100) ?>%; background: <?= $sumaActual === 100.00 ? 'var(--accent-green)' : ($sumaActual > 100.00 ? 'var(--accent-red)' : 'linear-gradient(to right, var(--accent-blue), var(--accent-indigo))') ?>;"></div>
                </div>
            </div>
            <?php if ($sumaActual < 100.00): ?>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.5rem;">
                    💡 Aún faltan <?= 100.00 - $sumaActual ?>% para completar el 100% del curso y poder generar notas definitivas balanceadas.
                </p>
            <?php elseif ($sumaActual === 100.00): ?>
                <p style="color: var(--accent-green); font-size: 0.85rem; margin-top: 0.5rem; font-weight: 500;">
                    🎉 ¡Perfecto! Los porcentajes de los cohortes suman exactamente el 100%.
                </p>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem; align-items: start;">
            <!-- Formulario de Cohortes (PANTALLA 4) -->
            <div class="glass-card" style="padding: 2rem;">
                <h2><?= $cohorteEditar ? 'Editar Cohorte' : 'Crear Nota / Cohorte' ?></h2>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem;">
                    Configure los ponderados de evaluación del curso.
                </p>

                <form action="index.php?c=Nota&a=cohortes" method="POST">
                    <!-- Hidden field para edición -->
                    <?php if ($cohorteEditar): ?>
                        <input type="hidden" name="nota_id" value="<?= htmlspecialchars($cohorteEditar['nota']) ?>">
                    <?php endif; ?>

                    <!-- Posición -->
                    <div class="form-group">
                        <label for="posicion">Posición (Orden)</label>
                        <input type="number" id="posicion" name="posicion" min="1" placeholder="Ej. 1" 
                               value="<?= $cohorteEditar ? htmlspecialchars($cohorteEditar['posicion']) : (count($cohortes) + 1) ?>" required>
                    </div>

                    <!-- Descripción -->
                    <div class="form-group">
                        <label for="descripcion">Descripción (Nombre)</label>
                        <input type="text" id="descripcion" name="descripcion" placeholder="Ej. Parcial uno" 
                               value="<?= $cohorteEditar ? htmlspecialchars($cohorteEditar['desc_nota']) : '' ?>" required>
                    </div>

                    <!-- Porcentaje -->
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="porcentaje">Porcentaje (%)</label>
                        <input type="number" id="porcentaje" name="porcentaje" min="0" max="100" step="0.01" placeholder="Ej. 30" 
                               value="<?= $cohorteEditar ? htmlspecialchars($cohorteEditar['porcentaje']) : '' ?>" required>
                    </div>

                    <!-- Botones de Acción -->
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 0.75rem;">
                        <?= $cohorteEditar ? '💾 Guardar Cambios' : '➕ Crear Nota Parcial' ?>
                    </button>

                    <?php if ($cohorteEditar): ?>
                        <a href="index.php?c=Nota&a=cohortes" class="btn btn-secondary" style="width: 100%;">
                            Cancelar Edición
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabla de Cohortes Registrados (PANTALLA 4) -->
            <div class="glass-card" style="padding: 2rem; margin-bottom: 0;">
                <h2>Notas y Cohortes Configurados</h2>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Estructura de evaluación definida para el curso activo.
                </p>

                <?php if (empty($cohortes)): ?>
                    <div style="text-align: center; padding: 4rem 1rem; color: var(--text-secondary);">
                        <span style="font-size: 3rem; display: block; margin-bottom: 1rem;">⚙️</span>
                        Aún no has configurado ninguna nota parcial o cohorte para este curso.
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 80px; text-align: center;">Posición</th>
                                    <th>Descripción</th>
                                    <th>Porcentaje (%)</th>
                                    <th>Código Nota</th>
                                    <th style="width: 180px; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cohortes as $ch): ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: 600;"><?= htmlspecialchars($ch['posicion']) ?></td>
                                        <td style="font-weight: 500;"><?= htmlspecialchars($ch['desc_nota']) ?></td>
                                        <td style="font-weight: 600; color: var(--accent-blue);"><?= htmlspecialchars($ch['porcentaje']) ?>%</td>
                                        <td style="font-family: monospace; color: var(--text-secondary); font-size: 0.85rem;"><?= htmlspecialchars($ch['nota']) ?></td>
                                        <td style="text-align: center; display: flex; gap: 0.5rem; justify-content: center;">
                                            <a href="index.php?c=Nota&a=cohortes&edit_nota=<?= urlencode($ch['nota']) ?>" 
                                               class="btn btn-secondary" 
                                               style="padding: 0.4rem 0.6rem; font-size: 0.85rem;"
                                               title="Editar Cohorte">
                                                ✏️ Editar
                                            </a>
                                            <a href="index.php?c=Nota&a=eliminar_cohorte&nota=<?= urlencode($ch['nota']) ?>" 
                                               class="btn btn-danger" 
                                               style="padding: 0.4rem 0.6rem; font-size: 0.85rem;" 
                                               onclick="return confirm('¿Está seguro de eliminar este cohorte? Esto borrará permanentemente todas las calificaciones de los estudiantes cargadas en él.');"
                                               title="Borrar Cohorte">
                                                🗑️ Borrar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
