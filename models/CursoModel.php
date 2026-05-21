<?php
class CursoModel {
    private $db;

    public function __construct() {
        // Instanciamos la clase Conexion y obtenemos el objeto PDO
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    /**
     * Valida las credenciales del docente contra la base de datos
     * @param string $cod_doc Código del docente
     * @param string $clave Contraseña
     * @return array|false Retorna el registro del docente si es válido, o false si no
     */
    public function validarDocente($cod_doc, $clave) {
        $sql = "SELECT * FROM docentes WHERE cod_doc = :cod_doc AND clave = :clave";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cod_doc' => $cod_doc,
            ':clave'   => $clave
        ]);
        return $stmt->fetch();
    }

    /**
     * Obtiene los cursos asignados a un docente específico
     * @param string $cod_doc Código del docente
     * @return array Lista de cursos
     */
    public function obtenerCursosDocente($cod_doc) {
        $sql = "SELECT * FROM cursos WHERE cod_doc = :cod_doc ORDER BY nomb_cur ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_doc' => $cod_doc]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los detalles de un curso por su código
     * @param string $cod_cur Código del curso
     * @return array|false Detalles del curso
     */
    public function obtenerCursoPorCodigo($cod_cur) {
        $sql = "SELECT * FROM cursos WHERE cod_cur = :cod_cur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_cur' => $cod_cur]);
        return $stmt->fetch();
    }

    /**
     * Obtiene los años de las inscripciones del docente para el menú desplegable
     * @param string $cod_doc Código del docente
     * @return array Lista de años
     */
    public function obtenerYearsDeDocente($cod_doc) {
        $sql = "SELECT DISTINCT i.year 
                FROM inscripciones i 
                INNER JOIN cursos c ON i.cod_cur = c.cod_cur 
                WHERE c.cod_doc = :cod_doc 
                ORDER BY i.year DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_doc' => $cod_doc]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
