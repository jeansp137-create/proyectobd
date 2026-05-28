<?php
class CursoModel {
    private $db;

    public function __construct() {
        
        $conexion = Conexion::getInstancia();
        $this->db = $conexion->conectar();
    }

    
    public function validarDocente($cod_doc, $clave) {
        $sql = "SELECT * FROM docentes WHERE cod_doc = :cod_doc AND clave = :clave";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cod_doc' => $cod_doc,
            ':clave'   => $clave
        ]);
        return $stmt->fetch();
    }

    
    public function obtenerCursosDocente($cod_doc) {
        $sql = "SELECT * FROM cursos WHERE cod_doc = :cod_doc ORDER BY nomb_cur ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_doc' => $cod_doc]);
        return $stmt->fetchAll();
    }

    
    public function obtenerCursoPorCodigo($cod_cur) {
        $sql = "SELECT * FROM cursos WHERE cod_cur = :cod_cur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_cur' => $cod_cur]);
        return $stmt->fetch();
    }

    
    public function obtenerYearsDeDocente($cod_doc) {
        $sql = "SELECT DISTINCT i.year 
                FROM inscripciones i 
                INNER JOIN cursos c ON i.cod_cur = c.cod_cur 
                WHERE c.cod_doc = :cod_doc 
                ORDER BY i.year DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_doc' => $cod_doc]);
        $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        
        if (empty($years)) {
            return [date('Y')];
        }
        return $years;
    }

    
    
    

    
    public function registrarCurso($cod_cur, $nomb_cur, $cod_doc) {
        
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

    
    public function actualizarCurso($cod_cur, $nomb_cur) {
        $sql = "UPDATE cursos SET nomb_cur = :nomb_cur WHERE cod_cur = :cod_cur";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nomb_cur' => $nomb_cur,
            ':cod_cur'  => $cod_cur
        ]);
    }

    
    public function eliminarCurso($cod_cur) {
        $sql = "DELETE FROM cursos WHERE cod_cur = :cod_cur";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':cod_cur' => $cod_cur]);
    }
}
?>
