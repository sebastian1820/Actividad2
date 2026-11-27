<?php
// ============================================================================
// controllers/UsuarioController.php
// ----------------------------------------------------------------------------
// CONTROLADOR encargado del CRUD de la tabla "usuarios" (cuentas del
// sistema). A diferencia del registro público (AuthController::registrar,
// que solo permite crear cuentas "aprendiz" o "coordinador"), este
// controlador es EXCLUSIVO del rol "coordinador" y sí permite crear
// cualquier rol, incluido "docente" (por eso el coordinador es quien da
// de alta a los docentes, como pidió el proyecto).
// ============================================================================

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../config/Auth.php";
require_once __DIR__ . "/../models/UsuarioModel.php";
require_once __DIR__ . "/../models/AprendizModel.php";

class UsuarioController
{
    private $modelo;
    private $modeloAprendiz;

    public function __construct()
    {
        $db = new Database();
        $conexionPDO = $db->getConexion();
        $this->modelo = new UsuarioModel($conexionPDO);
        $this->modeloAprendiz = new AprendizModel($conexionPDO);
    }

    // ------------------------------------------------------------------
    // Acción: listar todas las cuentas del sistema.
    // Permiso: SOLO coordinador (gestionar usuarios es una acción sensible).
    // ------------------------------------------------------------------
    public function listar()
    {
        Auth::requerirRol(["coordinador"]);

        $usuarios = $this->modelo->obtenerTodos();

        require __DIR__ . "/../views/usuarios/listar.php";
    }

    // ------------------------------------------------------------------
    // Acción: mostrar el formulario vacío para crear una cuenta nueva
    // (aprendiz, docente o coordinador).
    // ------------------------------------------------------------------
    public function mostrarFormularioCrear()
    {
        Auth::requerirRol(["coordinador"]);

        $errores = [];
        $datos = ["nombre" => "", "usuario" => "", "correo" => "", "rol" => "docente", "ficha" => ""];

        // Aprendices que aún no tienen cuenta, por si el coordinador quiere
        // vincular la nueva cuenta "aprendiz" a un registro ya existente
        // en vez de crear una ficha nueva.
        $aprendicesSinVincular = $this->modeloAprendiz->obtenerSinVincular();

        require __DIR__ . "/../views/usuarios/formulario_crear.php";
    }

    // ------------------------------------------------------------------
    // Acción: procesar el envío (POST) del formulario de creación.
    // ------------------------------------------------------------------
    public function guardar()
    {
        Auth::requerirRol(["coordinador"]);

        $errores = [];
        $datos = [
            "nombre"  => trim($_POST["nombre"] ?? ""),
            "usuario" => trim($_POST["usuario"] ?? ""),
            "correo"  => trim($_POST["correo"] ?? ""),
            "rol"     => trim($_POST["rol"] ?? ""),
            "ficha"   => trim($_POST["ficha"] ?? ""),
        ];
        $aprendizExistenteId = $_POST["aprendiz_existente_id"] ?? "";
        $contrasena          = $_POST["contrasena"] ?? "";
        $confirmarContrasena = $_POST["confirmar_contrasena"] ?? "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // --- Validaciones básicas ---------------------------------------
            if ($datos["nombre"] === "") {
                $errores[] = "El nombre es obligatorio.";
            }
            if ($datos["usuario"] === "") {
                $errores[] = "El nombre de usuario es obligatorio.";
            }
            if ($datos["correo"] === "") {
                $errores[] = "El correo es obligatorio.";
            } elseif (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
                $errores[] = "Escribe un correo electrónico válido.";
            }
            if (!in_array($datos["rol"], ["aprendiz", "docente", "coordinador"], true)) {
                $errores[] = "Selecciona un rol válido.";
            }
            if ($contrasena === "") {
                $errores[] = "La contraseña es obligatoria.";
            } elseif (strlen($contrasena) < 6) {
                $errores[] = "La contraseña debe tener mínimo 6 caracteres.";
            }
            if ($contrasena !== $confirmarContrasena) {
                $errores[] = "Las contraseñas no coinciden.";
            }

            // Si el rol es aprendiz, debe traer ficha nueva O un aprendiz existente.
            if ($datos["rol"] === "aprendiz" && $datos["ficha"] === "" && $aprendizExistenteId === "") {
                $errores[] = "Para un aprendiz, escribe una ficha nueva o selecciona uno existente.";
            }

            if (empty($errores)) {
                if ($this->modelo->existeUsuario($datos["usuario"])) {
                    $errores[] = "Ese nombre de usuario ya está en uso.";
                }
                if ($this->modelo->existeCorreo($datos["correo"])) {
                    $errores[] = "Ese correo ya está registrado.";
                }
            }

            if (empty($errores)) {
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $aprendizId = null;

                if ($datos["rol"] === "aprendiz") {
                    if ($datos["ficha"] !== "") {
                        // Se crea una ficha de aprendiz nueva.
                        $aprendizId = $this->modeloAprendiz->crear($datos["nombre"], $datos["ficha"]);
                    } elseif ($aprendizExistenteId !== "") {
                        // Se vincula a un aprendiz ya existente en la tabla.
                        $aprendizId = (int) $aprendizExistenteId;
                    }
                }

                $this->modelo->crear($datos["nombre"], $datos["usuario"], $datos["correo"], $hash, $datos["rol"]);

                // Se recupera la cuenta recién creada para poder vincular el
                // aprendiz_id, igual que en el registro público.
                if ($aprendizId !== null) {
                    $usuarioCreado = $this->modelo->obtenerPorUsuario($datos["usuario"]);
                    $this->modelo->actualizar(
                        $usuarioCreado["id"],
                        $datos["nombre"],
                        $datos["usuario"],
                        $datos["correo"],
                        $datos["rol"],
                        $aprendizId
                    );
                }

                header("Location: index.php?accion=usuarios");
                exit;
            }
        }

        $aprendicesSinVincular = $this->modeloAprendiz->obtenerSinVincular();
        require __DIR__ . "/../views/usuarios/formulario_crear.php";
    }

    // ------------------------------------------------------------------
    // Acción: mostrar el formulario de edición ya lleno.
    // ------------------------------------------------------------------
    public function mostrarFormularioEditar()
    {
        Auth::requerirRol(["coordinador"]);

        $id = $_GET["id"] ?? null;
        if ($id === null) {
            header("Location: index.php?accion=usuarios");
            exit;
        }

        $datos = $this->modelo->obtenerPorId($id);
        if (!$datos) {
            header("Location: index.php?accion=usuarios");
            exit;
        }

        // Se prepara este campo con el mismo nombre que usa el formulario,
        // para preseleccionar en el <select> el aprendiz ya vinculado (si lo hay).
        $datos["aprendiz_existente_id"] = $datos["aprendiz_id"] ?? "";

        $errores = [];
        $aprendicesSinVincular = $this->modeloAprendiz->obtenerDisponiblesParaUsuario($id);

        require __DIR__ . "/../views/usuarios/formulario_editar.php";
    }

    // ------------------------------------------------------------------
    // Acción: procesar el envío (POST) del formulario de edición.
    // ------------------------------------------------------------------
    public function actualizar()
    {
        Auth::requerirRol(["coordinador"]);

        $errores = [];
        $id = $_POST["id"] ?? null;

        $datos = [
            "nombre"  => trim($_POST["nombre"] ?? ""),
            "usuario" => trim($_POST["usuario"] ?? ""),
            "correo"  => trim($_POST["correo"] ?? ""),
            "rol"     => trim($_POST["rol"] ?? ""),
        ];
        $aprendizExistenteId = $_POST["aprendiz_existente_id"] ?? "";
        $contrasena          = $_POST["contrasena"] ?? ""; // Vacía = no cambiarla
        $confirmarContrasena = $_POST["confirmar_contrasena"] ?? "";

        if ($id === null) {
            header("Location: index.php?accion=usuarios");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            if ($datos["nombre"] === "") {
                $errores[] = "El nombre es obligatorio.";
            }
            if ($datos["usuario"] === "") {
                $errores[] = "El nombre de usuario es obligatorio.";
            }
            if ($datos["correo"] === "") {
                $errores[] = "El correo es obligatorio.";
            } elseif (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
                $errores[] = "Escribe un correo electrónico válido.";
            }
            if (!in_array($datos["rol"], ["aprendiz", "docente", "coordinador"], true)) {
                $errores[] = "Selecciona un rol válido.";
            }

            // La contraseña es OPCIONAL al editar: si se deja en blanco, se
            // conserva la actual; si se escribe algo, debe cumplir las reglas.
            if ($contrasena !== "" || $confirmarContrasena !== "") {
                if (strlen($contrasena) < 6) {
                    $errores[] = "La nueva contraseña debe tener mínimo 6 caracteres.";
                }
                if ($contrasena !== $confirmarContrasena) {
                    $errores[] = "Las contraseñas no coinciden.";
                }
            }

            if (empty($errores)) {
                if ($this->modelo->existeUsuario($datos["usuario"], $id)) {
                    $errores[] = "Ese nombre de usuario ya está en uso.";
                }
                if ($this->modelo->existeCorreo($datos["correo"], $id)) {
                    $errores[] = "Ese correo ya está registrado.";
                }
            }

            if (empty($errores)) {
                // Si el rol ya no es "aprendiz", se limpia el vínculo;
                // si sigue o pasa a serlo, se toma el aprendiz seleccionado.
                $aprendizId = ($datos["rol"] === "aprendiz" && $aprendizExistenteId !== "")
                    ? (int) $aprendizExistenteId
                    : null;

                if ($contrasena !== "") {
                    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
                    $this->modelo->actualizarConContrasena(
                        $id, $datos["nombre"], $datos["usuario"], $datos["correo"], $datos["rol"], $aprendizId, $hash
                    );
                } else {
                    $this->modelo->actualizar(
                        $id, $datos["nombre"], $datos["usuario"], $datos["correo"], $datos["rol"], $aprendizId
                    );
                }

                header("Location: index.php?accion=usuarios");
                exit;
            }
        }

        $datos["id"] = $id;
        $datos["aprendiz_existente_id"] = $aprendizExistenteId;
        $aprendicesSinVincular = $this->modeloAprendiz->obtenerDisponiblesParaUsuario($id);
        require __DIR__ . "/../views/usuarios/formulario_editar.php";
    }

    // ------------------------------------------------------------------
    // Acción: eliminar una cuenta de usuario.
    // ------------------------------------------------------------------
    public function eliminar()
    {
        Auth::requerirRol(["coordinador"]);

        $id = $_GET["id"] ?? null;

        // Un coordinador no puede eliminar su propia cuenta activa (evita
        // quedarse sin acceso al sistema por accidente).
        if ($id !== null && (int) $id !== (int) Auth::obtenerIdUsuario()) {
            $this->modelo->eliminar($id);
        }

        header("Location: index.php?accion=usuarios");
        exit;
    }
}
