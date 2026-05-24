<?php
class NotaModel {
    private $db;

    public function __construct() {
        $conexion = Conexion::getInstancia();
        $this->db = $conexion->conectar();
    }

    // ==========================================
    // SECCIÓN 1: GESTIÓN DE COHORTES (TABLA NOTAS)
    // ==========================================

    /**
     * Obtiene los cohortes configurados para un curso específico
     */
    public function obtenerNotasCurso($cod_cur) {
        $sql = "SELECT * FROM notas WHERE cod_cur = :cod_cur ORDER BY posicion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cod_cur' => $cod_cur]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un cohorte específico por su identificador (llave primaria)
     */
    public function obtenerCohortePorId($nota) {
        $sql = "SELECT * FROM notas WHERE nota = :nota";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nota' => $nota]);
        return $stmt->fetch();
    }

    /**
     * Calcula la suma de porcentajes de los cohortes de un curso (opcionalmente excluyendo uno)
     */
    public function obtenerSumaPorcentajeCurso($cod_cur, $excluir_nota = '') {
        if (!empty($excluir_nota)) {
            $sql = "SELECT SUM(porcentaje) FROM notas WHERE cod_cur = :cod_cur AND nota <> :excluir_nota";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cod_cur' => $cod_cur, ':excluir_nota' => $excluir_nota]);
        } else {
            $sql = "SELECT SUM(porcentaje) FROM notas WHERE cod_cur = :cod_cur";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cod_cur' => $cod_cur]);
        }
        return (float) $stmt->fetchColumn();
    }

    /**
     * Registra un nuevo cohorte en el curso
     */
    public function registrarCohorte($nota, $desc_nota, $porcentaje, $posicion, $cod_cur) {
        $sql = "INSERT INTO notas (nota, desc_nota, porcentaje, posicion, cod_cur) 
                VALUES (:nota, :desc_nota, :porcentaje, :posicion, :cod_cur)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nota'        => $nota,
            ':desc_nota'   => $desc_nota,
            ':porcentaje'  => $porcentaje,
            ':posicion'    => $posicion,
            ':cod_cur'     => $cod_cur
        ]);
    }

    /**
     * Actualiza la información de un cohorte existente
     */
    public function actualizarCohorte($nota, $desc_nota, $porcentaje, $posicion) {
        $sql = "UPDATE notas 
                SET desc_nota = :desc_nota, porcentaje = :porcentaje, posicion = :posicion 
                WHERE nota = :nota";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nota'        => $nota,
            ':desc_nota'   => $desc_nota,
            ':porcentaje'  => $porcentaje,
            ':posicion'    => $posicion
        ]);
    }

    /**
     * Elimina un cohorte por su identificador
     */
    public function eliminarCohorte($nota) {
        $sql = "DELETE FROM notas WHERE nota = :nota";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nota' => $nota]);
    }

    // ==========================================
    // SECCIÓN 2: CALIFICACIONES (TABLA CALIFICACIONES)
    // ==========================================

    /**
     * Obtiene las calificaciones existentes para los estudiantes inscritos en un curso
     * @return array Mapa estructurado [cod_est][nota_id] => valor de la nota
     */
    public function obtenerCalificacionesCurso($cod_cur, $year, $periodo) {
        $sql = "SELECT cod_est, nota, valor 
                FROM calificaciones 
                WHERE cod_cur = :cod_cur AND year = :year AND periodo = :periodo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cod_cur' => $cod_cur,
            ':year'    => $year,
            ':periodo' => $periodo
        ]);
        
        $calificaciones = [];
        while ($row = $stmt->fetch()) {
            $cod_est = $row['cod_est'];
            $nota = $row['nota'];
            $calificaciones[$cod_est][$nota] = (float) $row['valor'];
        }
        return $calificaciones;
    }

    /**
     * Guarda o actualiza la calificación de un estudiante en un cohorte específico
     */
    public function guardarOActualizarCalificacion($nota, $valor, $cod_cur, $cod_est, $year, $periodo) {
        // 1. Validar que el valor no sea negativo (Regla de integridad / Requisito del PDF)
        if ($valor < 0) {
            return false;
        }

        // 2. Verificar si ya existe un registro de calificación para este estudiante y cohorte
        $sqlCheck = "SELECT cod_cal FROM calificaciones 
                     WHERE nota = :nota AND cod_est = :cod_est AND cod_cur = :cod_cur AND year = :year AND periodo = :periodo";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([
            ':nota'    => $nota,
            ':cod_est' => $cod_est,
            ':cod_cur' => $cod_cur,
            ':year'    => $year,
            ':periodo' => $periodo
        ]);
        $cod_cal = $stmtCheck->fetchColumn();

        $fecha_actual = date('Y-m-d');

        if ($cod_cal) {
            // Si ya existe, actualizamos su nota
            $sql = "UPDATE calificaciones 
                    SET valor = :valor, fecha = :fecha 
                    WHERE cod_cal = :cod_cal";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':valor'   => $valor,
                ':fecha'   => $fecha_actual,
                ':cod_cal' => $cod_cal
            ]);
        } else {
            // Si no existe, creamos un nuevo registro
            $sql = "INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) 
                    VALUES (:nota, :valor, :fecha, :cod_cur, :cod_est, :year, :periodo)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nota'    => $nota,
                ':valor'   => $valor,
                ':fecha'   => $fecha_actual,
                ':cod_cur' => $cod_cur,
                ':cod_est' => $cod_est,
                ':year'    => $year,
                ':periodo' => $periodo
            ]);
        }
    }
}
?>
