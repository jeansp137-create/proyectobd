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
    $cursos = $this->cursoModel->obtenerCursosDocente($docente_id);
    $years  = $this->cursoModel->obtenerYearsDeDocente($docente_id);

    // Periodos disponibles según mes actual
    $mes_actual = (int) date('m');
    $periodos_disponibles = ['Periodo I'];
    if ($mes_actual >= 7) {
        $periodos_disponibles[] = 'Periodo II';
    }

    require_once 'views/cursos_docente.php';
}

    /**
     * PANTALLA 4: Visualización de Estudiantes Inscritos
     */
    public function estudiantes() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // ── VALIDACIÓN DE AÑO Y PERIODO ──────────────────────────
        $anio_enviado    = isset($_POST['year']) ? (int) trim($_POST['year']) : 0;
        $periodo_enviado = isset($_POST['periodo']) ? trim($_POST['periodo']) : '';
        $anio_actual     = (int) date('Y');
        $mes_actual      = (int) date('m');

        // No permitir años futuros
        if ($anio_enviado > $anio_actual) {
            $_SESSION['error_mensaje'] = "No se puede registrar en un año futuro.";
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        // No permitir Periodo II si aún no ha comenzado (antes de julio)
        if ($anio_enviado === $anio_actual && $periodo_enviado === 'Periodo II' && $mes_actual < 7) {
            $_SESSION['error_mensaje'] = "El Periodo II aún no ha iniciado.";
            header("Location: index.php?c=Curso&a=index");
            exit;
        }
        // ── FIN VALIDACIÓN ───────────────────────────────────────

        $_SESSION['curso_activo']   = isset($_POST['cod_cur']) ? trim($_POST['cod_cur']) : '';
        $_SESSION['year_activo']    = $anio_enviado;
        $_SESSION['periodo_activo'] = $periodo_enviado;
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

    // Obtenemos estudiantes inscritos (PANTALLA 4)
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
     * Acción para eliminar la inscripción de un estudiante
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

    // ==========================================
    // CRUD DE CURSOS
    // ==========================================

    /**
     * PANTALLA 3: Cursos Registrados (CRUD Cursos)
     */
    public function listar_cursos() {
        $docente_id = $_SESSION['docente_id'];
        $cursos = $this->cursoModel->obtenerCursosDocente($docente_id);

        $error = isset($_SESSION['error_mensaje']) ? $_SESSION['error_mensaje'] : '';
        $exito = isset($_SESSION['exito_mensaje']) ? $_SESSION['exito_mensaje'] : '';
        unset($_SESSION['error_mensaje'], $_SESSION['exito_mensaje']);

        $cursoEditar = null;
        if (isset($_GET['edit_cod_cur'])) {
            $cursoEditar = $this->cursoModel->obtenerCursoPorCodigo($_GET['edit_cod_cur']);
        }

        require_once 'views/cursos_registrados.php';
    }

    /**
     * Adicionar Curso (POST)
     */
    public function crear_curso() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cod_cur  = isset($_POST['cod_cur']) ? trim($_POST['cod_cur']) : '';
            $nomb_cur = isset($_POST['nomb_cur']) ? trim($_POST['nomb_cur']) : '';
            $docente_id = $_SESSION['docente_id'];

            if (!empty($cod_cur) && !empty($nomb_cur)) {
                $exito = $this->cursoModel->registrarCurso($cod_cur, $nomb_cur, $docente_id);
                if ($exito) {
                    $_SESSION['exito_mensaje'] = "Curso registrado correctamente.";
                } else {
                    $_SESSION['error_mensaje'] = "Error al registrar el curso. Es posible que el código del curso ya exista.";
                }
            } else {
                $_SESSION['error_mensaje'] = "Todos los campos son obligatorios.";
            }
        }
        header("Location: index.php?c=Curso&a=listar_cursos");
        exit;
    }

    /**
     * Editar Curso (POST / GET)
     */
    public function editar_curso() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // El campo cod_cur es de solo lectura en el formulario HTML (atributo readonly)
            // Aquí NO actualizamos cod_cur en el UPDATE para mantener la integridad referencial.
            $cod_cur  = isset($_POST['cod_cur']) ? trim($_POST['cod_cur']) : '';
            $nomb_cur = isset($_POST['nomb_cur']) ? trim($_POST['nomb_cur']) : '';

            if (!empty($cod_cur) && !empty($nomb_cur)) {
                $exito = $this->cursoModel->actualizarCurso($cod_cur, $nomb_cur);
                if ($exito) {
                    $_SESSION['exito_mensaje'] = "Curso actualizado correctamente.";
                } else {
                    $_SESSION['error_mensaje'] = "Error al actualizar el curso.";
                }
            } else {
                $_SESSION['error_mensaje'] = "El nombre del curso no puede estar vacío.";
            }
            header("Location: index.php?c=Curso&a=listar_cursos");
            exit;
        }

        // Si se llama por GET para cargar el formulario de edición
        $cod_cur = isset($_GET['cod_cur']) ? trim($_GET['cod_cur']) : '';
        header("Location: index.php?c=Curso&a=listar_cursos&edit_cod_cur=" . urlencode($cod_cur));
        exit;
    }

    /**
     * Eliminar Curso (GET)
     */
    public function eliminar_curso() {
        $cod_cur = isset($_GET['cod_cur']) ? trim($_GET['cod_cur']) : '';
        if (!empty($cod_cur)) {
            $exito = $this->cursoModel->eliminarCurso($cod_cur);
            if ($exito) {
                $_SESSION['exito_mensaje'] = "Curso eliminado correctamente.";
            } else {
                $_SESSION['error_mensaje'] = "Error al eliminar el curso.";
            }
        }
        header("Location: index.php?c=Curso&a=listar_cursos");
        exit;
    }

    // ==========================================
    // CARGA MASIVA DE ESTUDIANTES VIA CSV
    // ==========================================

    /**
     * PANTALLA 5: Formulario de carga de estudiantes (CSV)
     */
    public function cargar_estudiantes_vista() {
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $curso = $this->cursoModel->obtenerCursoPorCodigo($cod_cur);

        $error = isset($_SESSION['error_mensaje']) ? $_SESSION['error_mensaje'] : '';
        $exito = isset($_SESSION['exito_mensaje']) ? $_SESSION['exito_mensaje'] : '';
        unset($_SESSION['error_mensaje'], $_SESSION['exito_mensaje']);

        require_once 'views/cargar_estudiantes.php';
    }

    /**
     * Procesa el archivo CSV subido por el usuario
     */
    public function procesar_csv() {
        if (empty($_SESSION['curso_activo'])) {
            header("Location: index.php?c=Curso&a=index");
            exit;
        }

        $cod_cur = $_SESSION['curso_activo'];
        $year    = $_SESSION['year_activo'];
        $periodo = $_SESSION['periodo_activo'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
            $file = $_FILES['archivo_csv'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error_mensaje'] = "Error al subir el archivo.";
                header("Location: index.php?c=Curso&a=cargar_estudiantes_vista");
                exit;
            }

            // Validar extensión
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (strtolower($extension) !== 'csv') {
                $_SESSION['error_mensaje'] = "El archivo debe tener extensión .csv.";
                header("Location: index.php?c=Curso&a=cargar_estudiantes_vista");
                exit;
            }

            $filepath = $file['tmp_name'];
            $countNuevos = 0;
            $countMatriculados = 0;

            if (($handle = fopen($filepath, "r")) !== FALSE) {
                $firstRow = true;
                
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Si detecta punto y coma como delimitador en lugar de coma
                    if (count($data) === 1 && strpos($data[0], ';') !== false) {
                        $data = explode(';', $data[0]);
                    }

                    if (empty($data) || count($data) < 2) {
                        continue;
                    }

                    $cod_est  = trim($data[0]);
                    $nomb_est = trim($data[1]);

                    // Validar si la primera fila es de encabezados
                    if ($firstRow) {
                        $firstRow = false;
                        
                        // Si no es numérico el código de estudiante o contiene palabras comunes de encabezados
                        if (!is_numeric($cod_est) || 
                            stripos($cod_est, 'codigo') !== false || 
                            stripos($cod_est, 'código') !== false || 
                            stripos($nomb_est, 'nombre') !== false) {
                            continue; // Ignora los encabezados
                        }
                    }

                    if (!empty($cod_est) && !empty($nomb_est)) {
                        // 1. Verificar si el estudiante existe en el catálogo global
                        $estudiante = $this->estudianteModel->obtenerEstudiantePorCodigo($cod_est);
                        if (!$estudiante) {
                            $this->estudianteModel->registrarEstudianteSimple($cod_est, $nomb_est);
                            $countNuevos++;
                        }

                        // 2. Inscribir en el curso activo
                        $inscrito = $this->estudianteModel->inscribirEstudiante($cod_cur, $cod_est, $year, $periodo);
                        if ($inscrito) {
                            $countMatriculados++;
                        }
                    }
                }
                fclose($handle);

                $_SESSION['exito_mensaje'] = "Procesamiento completo. Estudiantes nuevos registrados: $countNuevos. Estudiantes matriculados exitosamente: $countMatriculados.";
            } else {
                $_SESSION['error_mensaje'] = "No se pudo abrir el archivo para lectura.";
            }
        } else {
            $_SESSION['error_mensaje'] = "No se ha seleccionado ningún archivo.";
        }

        header("Location: index.php?c=Curso&a=cargar_estudiantes_vista");
        exit;
    }
}
?>
