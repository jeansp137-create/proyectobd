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
    <title>Cursos Registrados - Registro de Notas</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <div class="header-top">
        REGISTRO DE NOTAS
        <span class="date-display"><?php echo htmlspecialchars($fecha_actual); ?></span>
    </div>
    <div class="header-bottom">
        CURSOS REGISTRADOS
    </div>

    <main class="app-container">
        <!-- Contenedor con estructura 70/30 -->
        <div class="panel-container">
            <!-- Columna Izquierda (70%) -->
            <div class="panel-left">
                
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

                <!-- Formulario de Adición o Edición Dinámica -->
                <?php if ($cursoEditar): ?>
                    <div style="background-color: #fcfcfc; border: 1px dashed #337ab7; padding: 15px; margin-bottom: 20px;">
                        <h3 style="font-size: 14px; color: #337ab7; margin-bottom: 15px; font-weight: 700;">EDITAR DETALLES DEL CURSO</h3>
                        <form action="index.php?c=Curso&a=editar_curso" method="POST">
                            <div class="form-group-row">
                                <label for="cod_cur">Código del Curso:</label>
                                <div class="input-wrapper">
                                    <input type="text" id="cod_cur" name="cod_cur" value="<?= htmlspecialchars($cursoEditar['cod_cur']) ?>" readonly style="background-color: #eeeeee; cursor: not-allowed;">
                                </div>
                            </div>
                            <div class="form-group-row">
                                <label for="nomb_cur">* Nombre del Curso:</label>
                                <div class="input-wrapper">
                                    <input type="text" id="nomb_cur" name="nomb_cur" value="<?= htmlspecialchars($cursoEditar['nomb_cur']) ?>" required placeholder="Ej. Bases de Datos II">
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <a href="index.php?c=Curso&a=listar_cursos" class="btn btn-secondary" style="margin-right: 5px;">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                <?php elseif (isset($_GET['action_insert'])): ?>
                    <div style="background-color: #fcfcfc; border: 1px dashed #5cb85c; padding: 15px; margin-bottom: 20px;">
                        <h3 style="font-size: 14px; color: #5cb85c; margin-bottom: 15px; font-weight: 700;">REGISTRAR NUEVO CURSO</h3>
                        <form action="index.php?c=Curso&a=crear_curso" method="POST">
                            <div class="form-group-row">
                                <label for="cod_cur">* Código:</label>
                                <div class="input-wrapper">
                                    <input type="text" id="cod_cur" name="cod_cur" required placeholder="Ej. CUR-01">
                                </div>
                            </div>
                            <div class="form-group-row">
                                <label for="nomb_cur">* Nombre:</label>
                                <div class="input-wrapper">
                                    <input type="text" id="nomb_cur" name="nomb_cur" required placeholder="Ej. Bases de Datos I">
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <a href="index.php?c=Curso&a=listar_cursos" class="btn btn-secondary" style="margin-right: 5px;">Cancelar</a>
                                <button type="submit" class="btn btn-success">Insertar</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 class="section-title" style="margin-bottom: 0;">CATÁLOGO DE CURSOS</h2>
                    <?php if (!isset($_GET['action_insert']) && !$cursoEditar): ?>
                        <a href="index.php?c=Curso&a=listar_cursos&action_insert=1" class="btn btn-success">+ Insertar curso</a>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">Código</th>
                                <th style="width: 55%;">Nombre del Curso</th>
                                <th style="width: 10%; text-align: center;">Editar</th>
                                <th style="width: 10%; text-align: center;">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cursos)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #777777;">No tiene cursos registrados actualmente.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cursos as $cur): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($cur['cod_cur']) ?></strong></td>
                                        <td><?= htmlspecialchars($cur['nomb_cur']) ?></td>
                                        <td style="text-align: center;">
                                            <a href="index.php?c=Curso&a=editar_curso&cod_cur=<?= urlencode($cur['cod_cur']) ?>" class="icon-action icon-blue" title="Editar curso">✏️</a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="index.php?c=Curso&a=eliminar_curso&cod_cur=<?= urlencode($cur['cod_cur']) ?>" class="icon-action icon-red" onclick="return confirm('¿Está seguro de eliminar este curso? Se eliminarán todas las inscripciones y notas asociadas en cascada.');" title="Eliminar curso">🗑️</a>
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
                
                <a href="index.php?c=Curso&a=index" class="btn btn-option">Volver a selección</a>
                <a href="index.php?c=Auth&a=logout" class="btn btn-option" style="background-color: #555555; border-color: #444444;">Cerrar sesión</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
