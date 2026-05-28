<?php
class AuthController {
    private $model;

    public function __construct() {
        
        $this->model = new CursoModel();
    }

    
    public function index() {
        $error = '';

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cod_doc = isset($_POST['cod_doc']) ? trim($_POST['cod_doc']) : '';
            $clave   = isset($_POST['clave']) ? trim($_POST['clave']) : '';

            if (!empty($cod_doc) && !empty($clave)) {
                
                $docente = $this->model->validarDocente($cod_doc, $clave);

                if ($docente) {
                    
                    $_SESSION['docente_id']     = $docente['cod_doc'];
                    $_SESSION['docente_nombre'] = $docente['nomb_doc'];

                    
                    header("Location: index.php?c=Curso&a=index");
                    exit;
                } else {
                    $error = "El código de docente o la clave son incorrectos.";
                }
            } else {
                $error = "Por favor, complete todos los campos.";
            }
        }

        
        require_once 'views/login.php';
    }

    
    public function logout() {
        
        session_destroy();
        
        
        header("Location: index.php?c=Auth&a=index");
        exit;
    }
}
?>
