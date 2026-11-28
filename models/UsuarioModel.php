<?php
// ============================================================================
// models/UsuarioModel.php
// ----------------------------------------------------------------------------
// MODELO encargado únicamente de la tabla "usuarios" (login y roles).
// Igual que AprendizModel, aquí vive todo el SQL relacionado a usuarios;
// ni el Controlador ni la Vista deben tocar la base de datos directamente.
// ============================================================================

class UsuarioModel
{
    // Guarda la conexión PDO recibida desde afuera.
    private $conexion;

    // Constructor: recibe el PDO ya conectado (igual patrón que AprendizModel).
    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // ------------------------------------------------------------------
    // Busca un usuario por su nombre de usuario (login). Se usa para
    // validar las credenciales cuando alguien intenta iniciar sesión.
    // Se incluye aprendiz_id porque, si el usuario tiene rol "aprendiz",
    // ese valor indica CUÁL fila de la tabla "aprendices" es su propia
    // información (para el dashboard personal de ese rol).
    // ------------------------------------------------------------------
    public function obtenerPorUsuario($usuario)
    {
        // Se seleccionan las columnas necesarias para el login y el dashboard.
        $sql = "SELECT id, nombre, usuario, correo, contrasena, rol, aprendiz_id
                FROM usuarios WHERE usuario = :usuario";

        // Se prepara la consulta con parámetro nombrado (evita inyección SQL).
        $consulta = $this->conexion->prepare($sql);

        // Se ejecuta enviando el valor real escrito por el usuario en el formulario.
        $consulta->execute(["usuario" => $usuario]);

        // fetch() retorna la fila encontrada, o false si ese usuario no existe.
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Busca un usuario por su CORREO. Es el punto de partida de la
    // recuperación de contraseña: la persona escribe su correo, no su
    // usuario, porque puede haber olvidado ambos datos.
    // ------------------------------------------------------------------
    public function obtenerPorCorreo($correo)
    {
        $sql = "SELECT id, nombre, usuario, correo, rol FROM usuarios WHERE correo = :correo";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["correo" => $correo]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Guarda el token de recuperación y su fecha de expiración para un
    // usuario. Se llama justo después de generar el token en el
    // Controlador, antes de enviar el correo con el enlace.
    // ------------------------------------------------------------------
    public function guardarTokenRecuperacion($id, $token, $expira)
    {
        $sql = "UPDATE usuarios
                SET token_recuperacion = :token, token_expira = :expira
                WHERE id = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "token"  => $token,  // Cadena aleatoria única que viajará en el enlace del correo
            "expira" => $expira, // Fecha/hora límite hasta la que el token es válido
            "id"     => $id,
        ]);
    }

    // ------------------------------------------------------------------
    // Busca un usuario a partir de un token de recuperación, pero SOLO si
    // ese token todavía no expiró. Se usa cuando la persona hace clic en
    // el enlace del correo y llega al formulario de nueva contraseña.
    // ------------------------------------------------------------------
    public function obtenerPorTokenValido($token)
    {
        // NOW() es la fecha/hora actual del servidor de base de datos;
        // se compara contra token_expira para descartar enlaces vencidos.
        $sql = "SELECT id, nombre, usuario, correo
                FROM usuarios
                WHERE token_recuperacion = :token AND token_expira > NOW()";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["token" => $token]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Actualiza la contraseña (ya con su nuevo hash) y BORRA el token de
    // recuperación, para que ese mismo enlace no se pueda volver a usar
    // una segunda vez (un token de un solo uso).
    // ------------------------------------------------------------------
    public function actualizarContrasenaYLimpiarToken($id, $contrasenaHash)
    {
        $sql = "UPDATE usuarios
                SET contrasena = :contrasena, token_recuperacion = NULL, token_expira = NULL
                WHERE id = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "contrasena" => $contrasenaHash, // Nueva contraseña, ya encriptada
            "id"         => $id,
        ]);
    }

    // ------------------------------------------------------------------
    // Cuenta cuántos usuarios existen para un rol específico. Se usa en
    // el dashboard del coordinador para mostrar totales (ej. "3 docentes").
    // ------------------------------------------------------------------
    public function contarPorRol($rol)
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE rol = :rol";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["rol" => $rol]);

        // fetchColumn() trae el número directamente (la primera columna del resultado).
        return (int) $consulta->fetchColumn();
    }

    // ------------------------------------------------------------------
    // Crea un nuevo usuario con contraseña ya encriptada (hash) y un rol.
    // Método pensado para que el coordinador pueda registrar nuevas cuentas
    // (aprendices, docentes u otros coordinadores) desde la aplicación.
    // ------------------------------------------------------------------
    public function crear($nombre, $usuario, $correo, $contrasenaHash, $rol)
    {
        $sql = "INSERT INTO usuarios (nombre, usuario, correo, contrasena, rol)
                VALUES (:nombre, :usuario, :correo, :contrasena, :rol)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "nombre"     => $nombre,         // Nombre completo de la persona
            "usuario"    => $usuario,        // Usuario con el que iniciará sesión
            "correo"     => $correo,         // Correo al que llegará el enlace si olvida su contraseña
            "contrasena" => $contrasenaHash, // Contraseña YA encriptada (nunca en texto plano)
            "rol"        => $rol,            // 'aprendiz', 'docente' o 'coordinador'
        ]);
    }

    // ------------------------------------------------------------------
    // Verifica si ya existe un usuario con ese nombre de login (para
    // validar duplicados antes de crear una cuenta nueva). El parámetro
    // opcional $idExcluir permite ignorar la propia fila cuando se está
    // EDITANDO un usuario (para no marcar como "duplicado" su propio
    // nombre de usuario actual, que no cambió).
    // ------------------------------------------------------------------
    public function existeUsuario($usuario, $idExcluir = null)
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario";
        $parametros = ["usuario" => $usuario];

        if ($idExcluir !== null) {
            $sql .= " AND id != :id";
            $parametros["id"] = $idExcluir;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        // fetchColumn() trae directamente el valor de la primera columna (el conteo).
        // Si es mayor que 0, significa que el usuario ya existe.
        return $consulta->fetchColumn() > 0;
    }

    // ------------------------------------------------------------------
    // Igual que existeUsuario(), pero validando el correo (también es
    // UNIQUE en la tabla). Se usa tanto en el registro público como en
    // el CRUD de usuarios del coordinador.
    // ------------------------------------------------------------------
    public function existeCorreo($correo, $idExcluir = null)
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo";
        $parametros = ["correo" => $correo];

        if ($idExcluir !== null) {
            $sql .= " AND id != :id";
            $parametros["id"] = $idExcluir;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchColumn() > 0;
    }

    // ------------------------------------------------------------------
    // Devuelve TODOS los usuarios registrados (usado por el listado/CRUD
    // que ve el coordinador). No se selecciona la columna "contrasena":
    // nunca debe viajar el hash hacia una vista, aunque no se muestre.
    // ------------------------------------------------------------------
    public function obtenerTodos()
    {
        $sql = "SELECT id, nombre, usuario, correo, rol, aprendiz_id, fecha_creacion
                FROM usuarios ORDER BY id DESC";
        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Busca UN usuario por su id (usado por el formulario de edición del
    // CRUD de usuarios). Tampoco trae la contraseña.
    // ------------------------------------------------------------------
    public function obtenerPorId($id)
    {
        $sql = "SELECT id, nombre, usuario, correo, rol, aprendiz_id
                FROM usuarios WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $id]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Actualiza los datos básicos de un usuario (sin tocar la contraseña).
    // Se usa cuando el coordinador edita una cuenta y deja el campo de
    // nueva contraseña en blanco (es decir, no quiere cambiarla).
    // ------------------------------------------------------------------
    public function actualizar($id, $nombre, $usuario, $correo, $rol, $aprendizId)
    {
        $sql = "UPDATE usuarios
                SET nombre = :nombre, usuario = :usuario, correo = :correo,
                    rol = :rol, aprendiz_id = :aprendiz_id
                WHERE id = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "nombre"      => $nombre,
            "usuario"     => $usuario,
            "correo"      => $correo,
            "rol"         => $rol,
            "aprendiz_id" => $aprendizId, // Puede ser null si el rol no es "aprendiz"
            "id"          => $id,
        ]);
    }

    // ------------------------------------------------------------------
    // Igual que actualizar(), pero además reemplaza la contraseña (ya
    // encriptada). Se usa cuando el coordinador SÍ escribió una nueva
    // contraseña en el formulario de edición.
    // ------------------------------------------------------------------
    public function actualizarConContrasena($id, $nombre, $usuario, $correo, $rol, $aprendizId, $contrasenaHash)
    {
        $sql = "UPDATE usuarios
                SET nombre = :nombre, usuario = :usuario, correo = :correo,
                    rol = :rol, aprendiz_id = :aprendiz_id, contrasena = :contrasena
                WHERE id = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "nombre"     => $nombre,
            "usuario"    => $usuario,
            "correo"     => $correo,
            "rol"        => $rol,
            "aprendiz_id" => $aprendizId,
            "contrasena" => $contrasenaHash,
            "id"         => $id,
        ]);
    }

    // ------------------------------------------------------------------
    // Elimina un usuario por su id (usado por el CRUD de usuarios del
    // coordinador). No borra la fila de "aprendices" vinculada, si la
    // hay: esa tabla es independiente (solo se desvincula si se elimina
    // desde ahí, gracias al ON DELETE SET NULL definido en el schema).
    // ------------------------------------------------------------------
    public function eliminar($id)
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute(["id" => $id]);
    }
}
