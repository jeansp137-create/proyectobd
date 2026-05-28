<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'config/conexion.php';


if (file_exists('models/CursoModel.php')) {
    require_once 'models/CursoModel.php';
}
if (file_exists('models/EstudianteModel.php')) {
    require_once 'models/EstudianteModel.php';
}
if (file_exists('models/NotaModel.php')) {
    require_once 'models/NotaModel.php';
}   



$c = isset($_GET['c']) ? $_GET['c'] : '';
$a = isset($_GET['a']) ? $_GET['a'] : '';


$controllerName = !empty($c) ? ucfirst(strtolower($c)) . 'Controller' : 'AuthController';
$action = !empty($a) ? strtolower($a) : 'index';




if (!isset($_SESSION['docente_id']) && $controllerName !== 'AuthController') {
    $controllerName = 'AuthController';
    $action = 'index';
}



if (isset($_SESSION['docente_id']) && $controllerName === 'AuthController' && $action === 'index') {
    header("Location: index.php?c=Curso&a=index");
    exit;
}


$controllerFile = 'controllers/' . $controllerName . '.php';


if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    
    if (class_exists($controllerName)) {
        $controllerObject = new $controllerName();
        
        
        if (method_exists($controllerObject, $action)) {
            
            $controllerObject->$action();
        } else {
            die("Error 404: La acción '{$action}' no se encuentra en el controlador '{$controllerName}'.");
        }
    } else {
        die("Error Interno: La clase controlador '{$controllerName}' no está definida dentro de '{$controllerFile}'.");
    }
} else {
    
    die("Error 404: El controlador solicitado '{$controllerName}' no existe. Ruta buscada: '{$controllerFile}'.");
}
?>