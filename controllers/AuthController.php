<?php
class AuthController {
    private $model;

    public function __construct() {
        // Usamos CursoModel para interactuar con la tabla docentes
        $this->model = new CursoModel();
    }

    /**
     * Muestra la pantalla de Login o procesa el formulario de inicio de sesión
     */
    public function index() {
        $error = '';

        // Si el formulario de login fue enviado por método POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cod_doc = isset($_POST['cod_doc']) ? trim($_POST['cod_doc']) : '';
            $clave   = isset($_POST['clave']) ? trim($_POST['clave']) : '';

            if (!empty($cod_doc) && !empty($clave)) {
                // Consultamos el docente en la base de datos
                $docente = $this->model->validarDocente($cod_doc, $clave);

                if ($docente) {
                    // Autenticación exitosa: Iniciamos sesión
                    $_SESSION['docente_id']     = $docente['cod_doc'];
                    $_SESSION['docente_nombre'] = $docente['nomb_doc'];

                    // Redireccionamos a la pantalla de cursos
                    header("Location: index.php?c=Curso&a=index");
                    exit;
                } else {
                    $error = "El código de docente o la clave son incorrectos.";
                }
            } else {
                $error = "Por favor, complete todos los campos.";
            }
        }

        // Cargamos la vista de Login (PANTALLA 1)
        require_once 'views/login.php';
    }

    /**
     * Cierra la sesión del docente y lo envía de vuelta al login
     */
    public function logout() {
        // Destruimos la sesión
        session_destroy();
        
        // Redireccionamos al login
        header("Location: index.php?c=Auth&a=index");
        exit;
    }
}
?>
