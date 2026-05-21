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
    <title>Cargar Estudiantes - Registro de Notas</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
    <div class="header-top">
        REGISTRO DE NOTAS
        <span class="date-display"><?php echo htmlspecialchars($fecha_actual); ?></span>
    </div>
    <div class="header-bottom">
        CARGAR ESTUDIANTES - <?= htmlspecialchars($curso['nomb_cur']) ?>
    </div>

    <main class="app-container">
        <!-- Estructura de Paneles 70/30 -->
        <div class="panel-container">
            <!-- Columna Izquierda (70%) -->
            <div class="panel-left">
                <h2 class="section-title">IMPORTACIÓN MASIVA DE ESTUDIANTES</h2>
                
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

                <p style="color: #666666; margin-bottom: 25px; font-size: 13px;">
                    Seleccione un archivo de texto plano separado por comas (.csv) que contenga la información de los estudiantes que desea matricular.
                </p>

                <!-- Formulario de Subida -->
                <form action="index.php?c=Curso&a=procesar_csv" method="POST" enctype="multipart/form-data">
                    <div class="form-group-row" style="margin-bottom: 25px;">
                        <label for="archivo_csv">* Seleccionar Archivo:</label>
                        <div class="input-wrapper">
                            <input type="file" id="archivo_csv" name="archivo_csv" accept=".csv" required style="border: 1px solid #cccccc; padding: 6px;">
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn btn-success" style="width: 140px;">Enviar archivo</button>
                    </div>
                </form>
            </div>

            <!-- Columna Derecha (30%) -->
            <div class="panel-right">
                <h3 class="panel-title">INFORMACIÓN</h3>
                
                <a href="index.php?c=Curso&a=estudiantes" class="btn btn-option">Volver a inscritos</a>
                
                <div class="recuadro-verde" style="margin-top: 15px;">
                    <strong>Formato del Archivo:</strong><br><br>
                    El archivo CSV debe tener un estudiante por renglón con los datos separados por comas (,) o punto y coma (;).<br><br>
                    Ejemplo:<br>
                    <code>6100025,Diana Gomez</code><br>
                    <code>6100026,Carlos Ruiz</code><br><br>
                    <strong>Nota:</strong> Si el archivo contiene una fila inicial de encabezado (ej. <code>código,nombre</code>), el sistema la detectará e ignorará automáticamente sin generar errores.
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Portal Docente - Universidad de los Llanos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
