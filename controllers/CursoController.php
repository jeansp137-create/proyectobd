<?php
class CursoController {
    private $cursoModel;
    private $estudianteModel;

    public function __construct() {
        $this->cursoModel = new CursoModel();
        $this->estudianteModel = new EstudianteModel();
    }

    /**
     * PANTALLA 2: Selección de Curso, Año y Periodo
     */
    public function index() {
        $docente_id = $_SESSION['docente_id'];

        // Obtenemos los cursos del docente
        $cursos = $this->cursoModel->obtenerCursosDocente($docente_id);
        
        // Obtenemos los años previos registrados para ayudar a auto-completar si lo desea
        $years = $this->cursoModel->obtenerYearsDeDocente($docente_id);

        // Cargamos la vista de selección
        require_once 'views/cursos_docente.php';
    }

    /**
     * PANTALLA 3: Visualización de Estudiantes Inscritos
     */
    public function estudiantes() {
        // Guardamos las selecciones en la sesión si se envían por POST desde la Selección (Pantalla 2)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['curso_activo']   = isset($_POST['cod_cur']) ? trim($_POST['cod_cur']) : '';
            $_SESSION['year_activo']    = isset($_POST['year']) ? (int) trim($_POST['year']) : 2022;
            $_SESSION['periodo_activo'] = isset($_POST['periodo']) ? trim($_POST['periodo']) : 'Periodo I';
        }

        // Si no hay selecciones en la sesión, lo devolvemos a la pantalla de selección
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $year    = $_SESSION['year_activo'];
        $periodo = $_SESSION['periodo_activo'];

        // Obtenemos detalles del curso
        $curso = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);

        // Obtenemos estudiantes inscritos (PANTALLA 3)
        $estudiantesInscritos = $this->estudianteModel->obtenerEstudiantesInscritos($cod_cur, $year, $periodo);

        // Obtenemos todos los estudiantes de la universidad para el formulario de inscripción
        $todosEstudiantes = $this->estudianteModel->obtenerTodosLosEstudiantes();

        // Cargamos la vista
        require_once 'views/estudiantes_inscritos.php';
    }

    /**
     * Acción para inscribir a un estudiante en el curso activo
     */
    public function inscribir() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cod_est = isset($_POST['cod_est']) ? trim($_POST['cod_est']) : '';
            
            $cod_cur = $_SESSION['curso_activo'];
            $year    = $_SESSION['year_activo'];
            $periodo = $_SESSION['periodo_activo'];

            if (!empty($cod_est) && !empty($cod_cur)) {
                $exito = $this->estudianteModel->inscribirEstudiante($cod_cur, $cod_est, $year, $periodo);
                if (!$exito) {
                    $_SESSION['error_mensaje'] = "El estudiante ya se encuentra inscrito en este curso.";
                } else {
                    $_SESSION['exito_mensaje'] = "Estudiante inscrito correctamente.";
                }
            }
        }
        header("Location: index.php?c=Curso&a=estudiantes");
        exit;
    }

    /**
     * Acción para eliminar la inscripción de un estudiante (Acción del botón rojo de papelera)
     */
    public function desinscribir() {
        $cod_est = isset($_GET['cod_est']) ? trim($_GET['cod_est']) : '';
        
        $cod_cur = $_SESSION['curso_activo'];
        $year    = $_SESSION['year_activo'];
        $periodo = $_SESSION['periodo_activo'];

        if (!empty($cod_est) && !empty($cod_cur)) {
            $exito = $this->estudianteModel->eliminarInscripcion($cod_cur, $cod_est, $year, $periodo);
            if ($exito) {
                $_SESSION['exito_mensaje'] = "Inscripción eliminada correctamente.";
            } else {
                $_SESSION['error_mensaje'] = "No se pudo eliminar la inscripción.";
            }
        }
        header("Location: index.php?c=Curso&a=estudiantes");
        exit;
    }
}
?>
