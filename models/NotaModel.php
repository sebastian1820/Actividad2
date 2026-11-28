<?php
// ============================================================================
// models/NotaModel.php
// ----------------------------------------------------------------------------
// MODELO encargado de la tabla "notas": las calificaciones que un docente
// (o coordinador) registra para cada aprendiz. Sigue el mismo patrón que
// AprendizModel y UsuarioModel: solo este archivo tiene SQL sobre "notas".
// ============================================================================

class NotaModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // ------------------------------------------------------------------
    // Inserta una nueva nota para un aprendiz. $docenteId es el id de la
    // cuenta de "usuarios" que la registró (solo para trazabilidad).
    // ------------------------------------------------------------------
    public function crear($aprendizId, $asignatura, $nota, $docenteId)
    {
        $sql = "INSERT INTO notas (aprendiz_id, asignatura, nota, docente_id)
                VALUES (:aprendiz_id, :asignatura, :nota, :docente_id)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "aprendiz_id" => $aprendizId,
            "asignatura"  => $asignatura,
            "nota"        => $nota,
            "docente_id"  => $docenteId,
        ]);
    }

    // ------------------------------------------------------------------
    // Devuelve todas las notas de UN aprendiz, con el nombre de quién la
    // registró (JOIN a usuarios), de la más reciente a la más antigua.
    // Se usa tanto en la pantalla del docente/coordinador (todas las notas
    // de ese aprendiz) como en "Mis notas" del propio aprendiz.
    // ------------------------------------------------------------------
    public function obtenerPorAprendiz($aprendizId)
    {
        $sql = "SELECT n.id, n.asignatura, n.nota, n.fecha_registro,
                       u.nombre AS docente_nombre
                FROM notas n
                LEFT JOIN usuarios u ON u.id = n.docente_id
                WHERE n.aprendiz_id = :aprendiz_id
                ORDER BY n.fecha_registro DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["aprendiz_id" => $aprendizId]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Busca UNA nota por su id (usado antes de eliminar, para confirmar
    // a qué aprendiz pertenece y poder regresar a esa pantalla después).
    // ------------------------------------------------------------------
    public function obtenerPorId($id)
    {
        $sql = "SELECT id, aprendiz_id, asignatura, nota FROM notas WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $id]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Elimina una nota por su id.
    // ------------------------------------------------------------------
    public function eliminar($id)
    {
        $sql = "DELETE FROM notas WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute(["id" => $id]);
    }
}
