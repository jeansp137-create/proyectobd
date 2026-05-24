<?php
class NotaController {
    private $cursoModel;
    private $estudianteModel;
    private $notaModel;

    public function __construct() {
        $this->cursoModel = new CursoModel();
        $this->estudianteModel = new EstudianteModel();
        $this->notaModel = new NotaModel();
    }

    /**
     * PANTALLA 4: Gestión de Cohortes (Notas parciales del curso)
     */
    public function cohortes() {
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $error   = '';
        $exito   = '';

        // Detalle del cohorte a editar si se solicita
        $cohorteEditar = null;
        if (isset($_GET['edit_nota'])) {
            $cohorteEditar = $this->notaModel->obtenerCohortePorId($_GET['edit_nota']);
        }

        // Si se envía el formulario para Agregar o Actualizar un cohorte
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nota_id     = isset($_POST['nota_id']) ? trim($_POST['nota_id']) : ''; // Para edición
            $posicion    = isset($_POST['posicion']) ? (int) trim($_POST['posicion']) : 1;
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $porcentaje  = isset($_POST['porcentaje']) ? (float) trim($_POST['porcentaje']) : 0.0;

            if (!empty($descripcion) && $porcentaje > 0 && $posicion > 0) {
                // Verificación de negocio: La suma total de porcentajes no debe exceder el 100%
                $sumaActual = $this->notaModel->obtenerSumaPorcentajeCurso($cod_cur, $nota_id);
                
                if (($sumaActual + $porcentaje) > 100.00) {
                    $error = "No se puede guardar. El porcentaje ingresado ({$porcentaje}%) hace que el total del curso sea " . ($sumaActual + $porcentaje) . "%, excediendo el 100% permitido.";
                } else {
                    if (!empty($nota_id)) {
                        // Modo Edición
                        $resultado = $this->notaModel->actualizarCohorte($nota_id, $descripcion, $porcentaje, $posicion);
                        if ($resultado) {
                            $exito = "Cohorte actualizado correctamente.";
                            // Limpiamos el modo edición de la URL
                            header("Location: index.php?c=Nota&a=cohortes&exito=" . urlencode($exito));
                            exit;
                        } else {
                            $error = "Error al actualizar el cohorte.";
                        }
                    } else {
                        // Modo Creación: Generamos un ID único y descriptivo
                        // Ejemplo: "N1_CUR-01" o similar, asegurándonos de que sea único
                        $nuevo_id = "N" . $posicion . "_" . $cod_cur . "_" . rand(10, 99);
                        $resultado = $this->notaModel->registrarCohorte($nuevo_id, $descripcion, $porcentaje, $posicion, $cod_cur);
                        if ($resultado) {
                            $exito = "Cohorte registrado correctamente.";
                        } else {
                            $error = "Error al registrar el cohorte. Verifique que la posición no esté repetida.";
                        }
                    }
                }
            } else {
                $error = "Todos los campos son obligatorios y los valores deben ser positivos.";
            }
        }

        // Si venimos de un redirect exitoso, capturamos el mensaje
        if (isset($_GET['exito'])) {
            $exito = $_GET['exito'];
        }

        // Cargamos los cohortes existentes para mostrarlos en la tabla
        $cohortes = $this->notaModel->obtenerNotasCurso($cod_cur);
        $curso = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);

        // Renderizamos la vista de gestión de cohortes (PANTALLA 4)
        require_once 'views/crear_notas.php';
    }

    /**
     * Elimina un cohorte y redirige a la lista
     */
    public function eliminar_cohorte() {
        $nota = isset($_GET['nota']) ? trim($_GET['nota']) : '';
        if (!empty($nota)) {
            $this->notaModel->eliminarCohorte($nota);
            $_SESSION['exito_mensaje'] = "Cohorte eliminado y calificaciones asociadas limpiadas.";
        }
        header("Location: index.php?c=Nota&a=cohortes");
        exit;
    }

    /**
     * PANTALLA 5: Registro y Actualización de Notas
     */
    public function registro() {
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $year    = $_SESSION['year_activo'];
        $periodo = $_SESSION['periodo_activo'];
        $error   = '';
        $exito   = '';

        // Si el profesor envía las notas digitas en la cuadrícula
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notas'])) {
            $notas_post = $_POST['notas']; // Array bidimensional: [cod_est][nota_id] => valor

            $totalGuardado = 0;
            $errorValidacion = false;

            foreach ($notas_post as $cod_est => $cohortes) {
                foreach ($cohortes as $nota_id => $valor_str) {
                    // Si la celda no está vacía
                    if ($valor_str !== '') {
                        $valor = (float) $valor_str;
                        // Restricción: valores no negativos
                        if ($valor >= 0.0 && $valor <= 5.0) {
                            $this->notaModel->guardarOActualizarCalificacion($nota_id, $valor, $cod_cur, $cod_est, $year, $periodo);
                            $totalGuardado++;
                        } else {
                            $errorValidacion = true;
                        }
                    }
                }
            }

            if ($errorValidacion) {
                $error = "Algunas notas fueron rechazadas. Recuerda que deben estar entre 0.0 y 5.0.";
            } else {
                $exito = "Calificaciones guardadas de manera exitosa en la base de datos.";
            }
        }

        // Cargamos los datos necesarios para pintar la cuadrícula
        $curso        = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);
        $estudiantes  = $this->estudianteModel->obtenerEstudiantesInscritos($cod_cur, $year, $periodo);
        $cohortes     = $this->notaModel->obtenerNotasCurso($cod_cur);
        
        // Calificaciones existentes en formato [cod_est][nota_id] => valor
        $calificaciones = $this->notaModel->obtenerCalificacionesCurso($cod_cur, $year, $periodo);

        // Renderizamos la vista de registro de notas (PANTALLA 5)
        require_once 'views/registro_notas.php';
    }

    /**
     * Acción para generar el Reporte Impreso / PDF
     */
    public function pdf() {
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $year    = $_SESSION['year_activo'];
        $periodo = $_SESSION['periodo_activo'];

        // Cargar datos
        $curso       = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);
        $estudiantes = $this->estudianteModel->obtenerEstudiantesInscritos($cod_cur, $year, $periodo);
        $cohortes    = $this->notaModel->obtenerNotasCurso($cod_cur);
        $calificaciones = $this->notaModel->obtenerCalificacionesCurso($cod_cur, $year, $periodo);

        // Mostrar la vista de impresión que gatilla window.print()
        require_once 'views/reporte_pdf.php';
    }
}
?>
