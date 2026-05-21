<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Curso - Portal Docente</title>
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
        <div style="max-width: 650px; margin: 3rem auto 0 auto;">
            <div class="glass-card">
                <h2>Selección de Curso Académico</h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                    Seleccione el curso a su cargo, el año respectivo y el periodo para comenzar a gestionar los estudiantes y calificaciones.
                </p>

                <form action="index.php?c=Curso&a=estudiantes" method="POST">
                    <!-- Selección del Curso -->
                    <div class="form-group">
                        <label for="cod_cur">Curso a su Cargo</label>
                        <select id="cod_cur" name="cod_cur" required>
                            <option value="">-- Seleccione un Curso --</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= htmlspecialchars($c['cod_cur']) ?>">
                                    <?= htmlspecialchars($c['cod_cur']) ?> - <?= htmlspecialchars($c['nomb_cur']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Año Académico -->
                    <div class="form-group">
                        <label for="year">Año Académico</label>
                        <input type="text" id="year" name="year" placeholder="Ej. 2022" value="2022" required>
                    </div>

                    <!-- Periodo Académico (Radio buttons) -->
                    <div class="form-group" style="margin-bottom: 2.5rem;">
                        <label>Periodo Semestral</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="periodo" value="Periodo I" checked>
                                <span>Periodo I</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="periodo" value="Periodo II">
                                <span>Periodo II</span>
                            </label>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        🔍 Ver listado
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
