<?php
class CursoModel {
    private $db;

    public function __construct() {
        // Obtenemos la instancia única de la clase Conexion y su objeto PDO
        $conexion = Conexion::getInstancia();
        $this->db = $conexion->conectar();
    }

    /**
     * Valida las credenciales del docente contra la base de datos
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
     */
    public function obtenerCursosDocente($cod_doc) {
        $sql = "SELECT * FROM cursos WHERE cod_doc = :cod_doc ORDER BY nomb_cur ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_doc' => $cod_doc]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los detalles de un curso por su código
     */
    public function obtenerCursoPorCodigo($cod_cur) {
        $sql = "SELECT * FROM cursos WHERE cod_cur = :cod_cur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_cur' => $cod_cur]);
        return $stmt->fetch();
    }

    /**
     * Obtiene los años de las inscripciones del docente para el menú desplegable
     */
    public function obtenerYearsDeDocente($cod_doc) {
        $sql = "SELECT DISTINCT i.year 
                FROM inscripciones i 
                INNER JOIN cursos c ON i.cod_cur = c.cod_cur 
                WHERE c.cod_doc = :cod_doc 
                ORDER BY i.year DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_doc' => $cod_doc]);
        $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Si no hay años registrados, por defecto retornamos el año actual para tener datos
        if (empty($years)) {
            return [date('Y')];
        }
        return $years;
    }

    // ==========================================
    // NUEVOS MÉTODOS CRUD PARA LA TABLA CURSOS
    // ==========================================

    /**
     * Inserta un nuevo curso en la base de datos
     */
    public function registrarCurso($cod_cur, $nomb_cur, $cod_doc) {
        // Validamos si ya existe el código para evitar duplicados
        $sqlCheck = "SELECT COUNT(*) FROM cursos WHERE cod_cur = :cod_cur";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':cod_cur' => $cod_cur]);
        if ($stmtCheck->fetchColumn() > 0) {
            return false;
        }

        $sql = "INSERT INTO cursos (cod_cur, nomb_cur, cod_doc) VALUES (:cod_cur, :nomb_cur, :cod_doc)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cod_cur'  => $cod_cur,
            ':nomb_cur' => $nomb_cur,
            ':cod_doc'  => $cod_doc
        ]);
    }

    /**
     * Actualiza un curso existente (NO altera cod_cur para proteger llaves foráneas)
     */
    public function actualizarCurso($cod_cur, $nomb_cur) {
        $sql = "UPDATE cursos SET nomb_cur = :nomb_cur WHERE cod_cur = :cod_cur";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nomb_cur' => $nomb_cur,
            ':cod_cur'  => $cod_cur
        ]);
    }

    /**
     * Elimina físicamente un curso por su código (las llaves foráneas en cascada borrarán el resto de dependencias)
     */
    public function eliminarCurso($cod_cur) {
        $sql = "DELETE FROM cursos WHERE cod_cur = :cod_cur";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':cod_cur' => $cod_cur]);
    }
}
?>
