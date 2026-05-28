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

    
    public function cohortes() {
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $error   = '';
        $exito   = '';

        
        $cohorteEditar = null;
        if (isset($_GET['edit_nota'])) {
            $cohorteEditar = $this->notaModel->obtenerCohortePorId($_GET['edit_nota']);
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nota_id     = isset($_POST['nota_id']) ? trim($_POST['nota_id']) : ''; 
            $posicion    = isset($_POST['posicion']) ? (int) trim($_POST['posicion']) : 1;
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $porcentaje  = isset($_POST['porcentaje']) ? (float) trim($_POST['porcentaje']) : 0.0;

            if (!empty($descripcion) && $porcentaje > 0 && $posicion > 0) {
                
                $sumaActual = $this->notaModel->obtenerSumaPorcentajeCurso($cod_cur, $nota_id);
                
                if (($sumaActual + $porcentaje) > 100.00) {
                    $error = "No se puede guardar. El porcentaje ingresado ({$porcentaje}%) hace que el total del curso sea " . ($sumaActual + $porcentaje) . "%, excediendo el 100% permitido.";
                } else {
                    if (!empty($nota_id)) {
                        
                        $resultado = $this->notaModel->actualizarCohorte($nota_id, $descripcion, $porcentaje, $posicion);
                        if ($resultado) {
                            $exito = "Cohorte actualizado correctamente.";
                            
                            header("Location: index.php?c=Nota&a=cohortes&exito=" . urlencode($exito));
                            exit;
                        } else {
                            $error = "Error al actualizar el cohorte.";
                        }
                    } else {
                        
                        
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

        
        if (isset($_GET['exito'])) {
            $exito = $_GET['exito'];
        }

        
        $cohortes = $this->notaModel->obtenerNotasCurso($cod_cur);
        $curso = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);

        
        require_once 'views/crear_notas.php';
    }

    
    public function eliminar_cohorte() {
        $nota = isset($_GET['nota']) ? trim($_GET['nota']) : '';
        if (!empty($nota)) {
            $this->notaModel->eliminarCohorte($nota);
            $_SESSION['exito_mensaje'] = "Cohorte eliminado y calificaciones asociadas limpiadas.";
        }
        header("Location: index.php?c=Nota&a=cohortes");
        exit;
    }

    
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

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notas'])) {
            $notas_post = $_POST['notas']; 

            $totalGuardado = 0;
            $errorValidacion = false;

            foreach ($notas_post as $cod_est => $cohortes) {
                foreach ($cohortes as $nota_id => $valor_str) {
                    
                    if ($valor_str !== '') {
                        $valor = (float) $valor_str;
                        
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

        
        $curso        = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);
        $estudiantes  = $this->estudianteModel->obtenerEstudiantesInscritos($cod_cur, $year, $periodo);
        $cohortes     = $this->notaModel->obtenerNotasCurso($cod_cur);
        
        
        $calificaciones = $this->notaModel->obtenerCalificacionesCurso($cod_cur, $year, $periodo);

        
        require_once 'views/registro_notas.php';
    }

    
    public function pdf() {
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $year    = $_SESSION['year_activo'];
        $periodo = $_SESSION['periodo_activo'];

        
        $curso       = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);
        $estudiantes = $this->estudianteModel->obtenerEstudiantesInscritos($cod_cur, $year, $periodo);
        $cohortes    = $this->notaModel->obtenerNotasCurso($cod_cur);
        $calificaciones = $this->notaModel->obtenerCalificacionesCurso($cod_cur, $year, $periodo);

        
        require_once 'views/reporte_pdf.php';
    }
}
?>
