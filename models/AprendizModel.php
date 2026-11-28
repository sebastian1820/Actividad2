<?php
// ============================================================================
// models/AprendizModel.php
// ----------------------------------------------------------------------------
// MODELO (la "M" de MVC).
// Responsabilidad única: hablar con la base de datos sobre la tabla
// "aprendices". Ninguna otra capa (Controlador o Vista) debe escribir SQL:
// todo lo relacionado con datos vive aquí. Así, si mañana cambia el motor
// de base de datos o la tabla, solo se modifica este archivo.
// ============================================================================

class AprendizModel
{
    // Guarda la conexión PDO que recibe desde afuera (inyección de dependencia).
    private $conexion;

    // Constructor: recibe un objeto PDO ya conectado (viene de Database.php)
    // y lo guarda para usarlo en todos los métodos de la clase.
    public function __construct($conexion)
    {
        $this->conexion = $conexion; // Se asigna el PDO recibido a la propiedad interna
    }

    // ------------------------------------------------------------------
    // Devuelve TODOS los aprendices registrados (usado por el listado).
    // ------------------------------------------------------------------
    public function obtenerTodos()
    {
        // Se prepara y ejecuta una consulta SELECT simple sobre toda la tabla.
        $consulta = $this->conexion->query("SELECT id, nombre, ficha, estado FROM aprendices ORDER BY id DESC");

        // fetchAll trae todas las filas como un arreglo asociativo (una fila = un array).
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Devuelve solo los últimos $limite aprendices registrados (usado en
    // los dashboards de docente y coordinador, para no mostrar la tabla
    // completa en una pantalla de resumen).
    // ------------------------------------------------------------------
    public function obtenerRecientes($limite = 5)
    {
        // (int) fuerza que $limite sea un número; como no se puede usar un
        // parámetro preparado dentro de LIMIT en todas las versiones de
        // MySQL, se valida el tipo aquí antes de concatenarlo en el SQL.
        $limite = (int) $limite;

        $sql = "SELECT id, nombre, ficha, estado FROM aprendices ORDER BY id DESC LIMIT $limite";
        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Cuenta cuántos aprendices hay en total (usado en el dashboard del
    // coordinador para mostrar la tarjeta de resumen "Total aprendices").
    // ------------------------------------------------------------------
    public function contarTotal()
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM aprendices");

        // fetchColumn() trae directamente el número (primera columna del resultado).
        return (int) $consulta->fetchColumn();
    }

    // ------------------------------------------------------------------
    // Busca UN aprendiz por su id (usado por el formulario de edición).
    // ------------------------------------------------------------------
    public function obtenerPorId($id)
    {
        // Se usa una consulta preparada con parámetro nombrado :id para
        // evitar inyección SQL (nunca se concatena el id directamente).
        $sql = "SELECT id, nombre, ficha, estado FROM aprendices WHERE id = :id";

        // prepare() compila la consulta dejando el marcador :id pendiente.
        $consulta = $this->conexion->prepare($sql);

        // execute() envía el valor real de $id de forma segura al motor SQL.
        $consulta->execute(["id" => $id]);

        // fetch() trae una sola fila (o false si no existe ese id).
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Devuelve los aprendices que TODAVÍA no tienen una cuenta de usuario
    // vinculada (no aparecen como aprendiz_id en la tabla "usuarios").
    // Se usa en el formulario de creación/edición de usuarios: cuando el
    // coordinador crea una cuenta con rol "aprendiz" sin marcar "crear
    // ficha nueva", puede escoger vincularla a un aprendiz ya existente.
    // ------------------------------------------------------------------
    public function obtenerSinVincular()
    {
        $sql = "SELECT a.id, a.nombre, a.ficha
                FROM aprendices a
                LEFT JOIN usuarios u ON u.aprendiz_id = a.id
                WHERE u.id IS NULL AND a.estado = 'activo'
                ORDER BY a.id DESC";
        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Igual que obtenerSinVincular(), pero además incluye el aprendiz que
    // YA está vinculado al usuario indicado (si tiene uno). Se usa en el
    // formulario de EDICIÓN de usuarios: si no se incluyera, el aprendiz
    // actualmente vinculado a esa cuenta "desaparecería" del <select> por
    // ya tener dueño, impidiendo dejarlo tal como está.
    // ------------------------------------------------------------------
    public function obtenerDisponiblesParaUsuario($usuarioId)
    {
        $sql = "SELECT a.id, a.nombre, a.ficha
                FROM aprendices a
                LEFT JOIN usuarios u ON u.aprendiz_id = a.id AND u.id != :usuario_id
                WHERE u.id IS NULL AND a.estado = 'activo'
                ORDER BY a.id DESC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["usuario_id" => $usuarioId]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Inserta un nuevo aprendiz (usado al guardar el formulario de registro).
    // ------------------------------------------------------------------
    public function crear($nombre, $ficha)
    {
        // Consulta INSERT con parámetros nombrados, igual de segura que arriba.
        $sql = "INSERT INTO aprendices (nombre, ficha) VALUES (:nombre, :ficha)";

        // Se prepara la sentencia.
        $consulta = $this->conexion->prepare($sql);

        // Se ejecuta enviando los valores reales; PDO se encarga de "escaparlos".
        $consulta->execute([
            "nombre" => $nombre, // Valor que llena el marcador :nombre
            "ficha"  => $ficha,  // Valor que llena el marcador :ficha
        ]);

        // Se retorna el ID autoincremental recién insertado (en vez de solo
        // true/false) porque el registro público de aprendices necesita
        // saber este id para vincularlo de inmediato a la cuenta de usuario
        // recién creada (columna usuarios.aprendiz_id).
        return (int) $this->conexion->lastInsertId();
    }

    // ------------------------------------------------------------------
    // Actualiza un aprendiz existente (usado al guardar la edición).
    // ------------------------------------------------------------------
    public function actualizar($id, $nombre, $ficha)
    {
        // Consulta UPDATE que modifica nombre y ficha del registro con ese id.
        $sql = "UPDATE aprendices SET nombre = :nombre, ficha = :ficha WHERE id = :id";

        // Se prepara la sentencia UPDATE.
        $consulta = $this->conexion->prepare($sql);

        // Se ejecuta enviando los tres valores necesarios.
        return $consulta->execute([
            "nombre" => $nombre, // Nuevo valor del nombre
            "ficha"  => $ficha,  // Nuevo valor de la ficha
            "id"     => $id,     // Identifica qué fila se actualiza
        ]);
    }

    // ------------------------------------------------------------------
    // Marca a un aprendiz como "retirado" SIN borrar su registro (a
    // diferencia de eliminar(), que sí borra la fila por completo). Así se
    // conserva su historial de notas y su cuenta de usuario sigue existiendo,
    // solo que deja de aparecer como candidato para nuevas vinculaciones.
    // Puede ejecutarla el docente o el coordinador (ver AprendizController).
    // ------------------------------------------------------------------
    public function inactivarPorRetiro($id)
    {
        $sql = "UPDATE aprendices SET estado = 'retirado' WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute(["id" => $id]);
    }

    // ------------------------------------------------------------------
    // Reactiva a un aprendiz previamente marcado como "retirado" (por si se
    // reintegra al programa de formación más adelante).
    // ------------------------------------------------------------------
    public function activar($id)
    {
        $sql = "UPDATE aprendices SET estado = 'activo' WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute(["id" => $id]);
    }

    // ------------------------------------------------------------------
    // Elimina un aprendiz por su id (usado por el enlace "Eliminar").
    // ------------------------------------------------------------------
    public function eliminar($id)
    {
        // Consulta DELETE filtrando por id.
        $sql = "DELETE FROM aprendices WHERE id = :id";

        // Se prepara la sentencia DELETE.
        $consulta = $this->conexion->prepare($sql);

        // Se ejecuta enviando el id a eliminar.
        return $consulta->execute(["id" => $id]);
    }
}
