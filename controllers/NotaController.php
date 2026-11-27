<?php
// ============================================================================
// controllers/NotaController.php
// ----------------------------------------------------------------------------
// CONTROLADOR encargado de subir/editar/eliminar notas de un aprendiz, y de
// mostrarle al propio aprendiz sus notas en modo solo-lectura.
//
// Reglas de permiso de esta funcionalidad:
//   - docente y coordinador: pueden VER las notas de cualquier aprendiz,
//     SUBIR notas nuevas, EDITAR una nota existente y ELIMINARLA.
//   - aprendiz: solo puede VER sus propias notas (misNotas()), nunca las
//     de otro aprendiz ni modificar ninguna.
// ============================================================================

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../config/Auth.php";
require_once __DIR__ . "/../models/NotaModel.php";
require_once __DIR__ . "/../models/AprendizModel.php";

class NotaController
{
    private $modelo;
    private $modeloAprendiz;

    public function __construct()
    {
        $db = new Database();
        $conexionPDO = $db->getConexion();
        $this->modelo = new NotaModel($conexionPDO);
        $this->modeloAprendiz = new AprendizModel($conexionPDO);
    }

    // ------------------------------------------------------------------
    // Acción: gestionar (ver + subir + eliminar) las notas de UN aprendiz.
    // Permiso: docente o coordinador.
    // ------------------------------------------------------------------
    public function gestionar()
    {
        Auth::requerirRol(["docente", "coordinador"]);

        $aprendizId = $_GET["aprendiz_id"] ?? null;

        if ($aprendizId === null) {
            header("Location: index.php?accion=listar");
            exit;
        }

        // Se busca al aprendiz para mostrar su nombre/ficha en la pantalla
        // y para confirmar que ese id realmente existe.
        $aprendiz = $this->modeloAprendiz->obtenerPorId($aprendizId);

        if (!$aprendiz) {
            header("Location: index.php?accion=listar");
            exit;
        }

        $notas = $this->modelo->obtenerPorAprendiz($aprendizId);
        $errores = [];

        require __DIR__ . "/../views/notas/gestionar.php";
    }

    // ------------------------------------------------------------------
    // Acción: procesar el envío (POST) del formulario para subir una nota.
    // Permiso: docente o coordinador.
    // ------------------------------------------------------------------
    public function guardar()
    {
        Auth::requerirRol(["docente", "coordinador"]);

        $aprendizId = $_POST["aprendiz_id"] ?? null;
        $asignatura = trim($_POST["asignatura"] ?? "");
        $nota       = trim($_POST["nota"] ?? "");

        if ($aprendizId !== null && $_SERVER["REQUEST_METHOD"] === "POST") {

            // Validación mínima: asignatura no vacía y nota numérica entre 0 y 5
            // (escala estándar de evaluación del SENA).
            if ($asignatura !== "" && is_numeric($nota) && $nota >= 0 && $nota <= 5) {
                $docenteId = Auth::obtenerIdUsuario();
                $this->modelo->crear($aprendizId, $asignatura, $nota, $docenteId);
            }

            header("Location: index.php?accion=notas&aprendiz_id=" . urlencode($aprendizId));
            exit;
        }
    }

    // ------------------------------------------------------------------
    // Acción: eliminar una nota. Permiso: docente o coordinador.
    // ------------------------------------------------------------------
    public function eliminar()
    {
        Auth::requerirRol(["docente", "coordinador"]);

        $id = $_GET["id"] ?? null;

        if ($id !== null) {
            // Se busca la nota ANTES de borrarla, solo para saber a qué
            // aprendiz pertenecía y poder regresar a esa misma pantalla.
            $nota = $this->modelo->obtenerPorId($id);
            $this->modelo->eliminar($id);

            if ($nota) {
                header("Location: index.php?accion=notas&aprendiz_id=" . urlencode($nota["aprendiz_id"]));
                exit;
            }
        }

        header("Location: index.php?accion=listar");
        exit;
    }

    // ------------------------------------------------------------------
    // Acción: el propio aprendiz consulta SUS notas (solo lectura).
    // Permiso: exclusivamente rol aprendiz.
    // ------------------------------------------------------------------
    public function misNotas()
    {
        Auth::requerirRol(["aprendiz"]);

        // Se toma el aprendiz_id vinculado a ESTA sesión (nunca de la URL),
        // para que sea imposible ver las notas de otro aprendiz cambiando
        // un parámetro en el navegador.
        $aprendizId = Auth::obtenerAprendizId();

        $notas = $aprendizId ? $this->modelo->obtenerPorAprendiz($aprendizId) : [];
        $vinculado = $aprendizId !== null;

        require __DIR__ . "/../views/notas/mis_notas.php";
    }
}
