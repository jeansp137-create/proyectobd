<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Iniciamos la sesión para poder guardar los datos del docente logueado
session_start();

// Importamos la conexión a la base de datos para que esté disponible globalmente
require_once 'config/conexion.php';

// Cargamos de forma sencilla los Modelos para que puedan ser usados por cualquier controlador
if (file_exists('models/CursoModel.php')) {
    require_once 'models/CursoModel.php';
}
if (file_exists('models/EstudianteModel.php')) {
    require_once 'models/EstudianteModel.php';
}
if (file_exists('models/NotaModel.php')) {
    require_once 'models/NotaModel.php';
}   

// Obtenemos el controlador (c) y la acción (a) desde la URL
// Ejemplo: index.php?c=Curso&a=index
$c = isset($_GET['c']) ? $_GET['c'] : '';
$a = isset($_GET['a']) ? $_GET['a'] : '';

// Normalizamos el nombre del controlador (Ejemplo: "curso" -> "CursoController")
$controllerName = !empty($c) ? ucfirst(strtolower($c)) . 'Controller' : 'AuthController';
$action = !empty($a) ? strtolower($a) : 'index';

// PROTECCIÓN DE RUTAS: 
// Si el docente no está logueado y el controlador al que intenta acceder no es el de Autenticación,
// forzamos el redireccionamiento al Login para proteger los datos de la universidad.
if (!isset($_SESSION['docente_id']) && $controllerName !== 'AuthController') {
    $controllerName = 'AuthController';
    $action = 'index';
}

// Si ya tiene sesión iniciada e intenta ir al AuthController a la acción 'index',
// lo redirigimos automáticamente a la pantalla de sus cursos para su comodidad.
if (isset($_SESSION['docente_id']) && $controllerName === 'AuthController' && $action === 'index') {
    header("Location: index.php?c=Curso&a=index");
    exit;
}

// Ruta del archivo del controlador
$controllerFile = 'controllers/' . $controllerName . '.php';

// Verificamos si el archivo del controlador físico existe
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Verificamos si la clase existe dentro del archivo
    if (class_exists($controllerName)) {
        $controllerObject = new $controllerName();
        
        // Verificamos si el método o acción existe en la clase del controlador
        if (method_exists($controllerObject, $action)) {
            // Ejecutamos la acción dinámicamente
            $controllerObject->$action();
        } else {
            die("Error 404: La acción '{$action}' no se encuentra en el controlador '{$controllerName}'.");
        }
    } else {
        die("Error Interno: La clase controlador '{$controllerName}' no está definida dentro de '{$controllerFile}'.");
    }
} else {
    // Si no existe, podría significar que el usuario digitó mal la URL
    die("Error 404: El controlador solicitado '{$controllerName}' no existe. Ruta buscada: '{$controllerFile}'.");
}
?>
