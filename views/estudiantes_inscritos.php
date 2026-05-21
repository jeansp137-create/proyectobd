<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes Inscritos - Portal Docente</title>
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
            <a href="index.php?c=Curso&a=estudiantes" class="nav-link active">👥 Estudiantes Inscritos</a>
            <a href="index.php?c=Nota&a=cohortes" class="nav-link">⚙️ Configurar Cohortes</a>
            <a href="index.php?c=Nota&a=registro" class="nav-link">📝 Registrar Calificaciones</a>
        </div>

        <!-- Mensajes de Alerta -->
        <?php if (isset($_SESSION['exito_mensaje'])): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <div><?= htmlspecialchars($_SESSION['exito_mensaje']) ?></div>
            </div>
            <?php unset($_SESSION['exito_mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_mensaje'])): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?= htmlspecialchars($_SESSION['error_mensaje']) ?></div>
            </div>
            <?php unset($_SESSION['error_mensaje']); ?>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; align-items: start;">
            <!-- Tabla de Estudiantes (PANTALLA 3) -->
            <div class="glass-card" style="padding: 2rem; margin-bottom: 0;">
                <h2>Estudiantes Inscritos en el Curso</h2>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Listado de alumnos matriculados en este grupo para el semestre en curso.
                </p>

                <?php if (empty($estudiantesInscritos)): ?>
                    <div style="text-align: center; padding: 3rem 1rem; color: var(--text-secondary);">
                        <span style="font-size: 3rem; display: block; margin-bottom: 1rem;">👥</span>
                        No hay estudiantes inscritos en este curso para este año/periodo.
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No.</th>
                                    <th>Código</th>
                                    <th>Nombres y Apellidos</th>
                                    <th style="width: 100px; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $contador = 1; 
                                foreach ($estudiantesInscritos as $est): 
                                ?>
                                    <tr>
                                        <td><?= $contador++ ?></td>
                                        <td style="font-weight: 600; color: var(--accent-blue);"><?= htmlspecialchars($est['cod_est']) ?></td>
                                        <td><?= htmlspecialchars($est['nomb_est']) ?></td>
                                        <td style="text-align: center;">
                                            <a href="index.php?c=Curso&a=desinscribir&cod_est=<?= urlencode($est['cod_est']) ?>" 
                                               class="btn btn-danger" 
                                               style="padding: 0.4rem 0.6rem; font-size: 0.85rem;" 
                                               onclick="return confirm('¿Está seguro de que desea eliminar la inscripción de este estudiante? Se eliminarán todas sus notas asociadas.');"
                                               title="Eliminar Inscripción">
                                                🗑️ Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Formulario de Inscripción Rápida -->
            <div class="glass-card" style="padding: 2rem;">
                <h2>Matricular Alumno</h2>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem;">
                    Inscriba estudiantes previamente registrados en la universidad en este curso.
                </p>

                <form action="index.php?c=Curso&a=inscribir" method="POST">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="cod_est">Seleccionar Estudiante</label>
                        <select id="cod_est" name="cod_est" required>
                            <option value="">-- Elija un Estudiante --</option>
                            <?php foreach ($todosEstudiantes as $est): ?>
                                <option value="<?= htmlspecialchars($est['cod_est']) ?>">
                                    [<?= htmlspecialchars($est['cod_est']) ?>] <?= htmlspecialchars($est['nomb_est']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success" style="width: 100%;">
                        ➕ Inscribir Alumno
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
