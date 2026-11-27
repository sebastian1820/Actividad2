<?php
// ============================================================================
// controllers/DashboardController.php
// ----------------------------------------------------------------------------
// CONTROLADOR encargado de la pantalla de inicio después del login. En vez de
// mandar a todos los roles al mismo listado, aquí se decide QUÉ VISTA de
// dashboard corresponde según Auth::obtenerRol(), y se le entrega a esa vista
// solo los datos que necesita. Cada rol tiene su propia carpeta de vista
// (views/dashboard/coordinador.php, docente.php, aprendiz.php).
// ============================================================================

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../config/Auth.php";
require_once __DIR__ . "/../models/AprendizModel.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

class DashboardController
{
    // Se guardan ambos modelos porque los distintos dashboards necesitan
    // datos tanto de aprendices como de usuarios (para las estadísticas).
    private $modeloAprendiz;
    private $modeloUsuario;

    // Constructor: arma la conexión y ambos modelos.
    public function __construct()
    {
        $db = new Database();
        $conexionPDO = $db->getConexion();
        $this->modeloAprendiz = new AprendizModel($conexionPDO);
        $this->modeloUsuario  = new UsuarioModel($conexionPDO);
    }

    // ------------------------------------------------------------------
    // Punto de entrada único: exige sesión y luego reparte según el rol.
    // ------------------------------------------------------------------
    public function mostrarDashboard()
    {
        // Cualquier rol autenticado puede ver SU dashboard (solo cambia cuál).
        Auth::requerirAutenticacion();

        // Se lee el rol guardado en sesión al iniciar sesión.
        $rol = Auth::obtenerRol();

        // Según el rol, se delega en el método privado correspondiente.
        // Esto evita un "if gigante" repetido en cada acción del sistema:
        // el enrutamiento por rol queda concentrado en un solo lugar.
        switch ($rol) {
            case "coordinador":
                $this->mostrarDashboardCoordinador();
                break;

            case "docente":
                $this->mostrarDashboardDocente();
                break;

            case "aprendiz":
                $this->mostrarDashboardAprendiz();
                break;

            default:
                // Si por alguna razón el rol no es ninguno conocido, se
                // manda al listado general como opción segura.
                header("Location: index.php?accion=listar");
                exit;
        }
    }

    // ------------------------------------------------------------------
    // Dashboard del COORDINADOR: panorama general del sistema completo.
    // ------------------------------------------------------------------
    private function mostrarDashboardCoordinador()
    {
        // Total de aprendices registrados en el sistema.
        $totalAprendices = $this->modeloAprendiz->contarTotal();

        // Conteo de usuarios por cada rol, para dar una foto completa
        // de quiénes tienen cuenta en la plataforma.
        $totalDocentes      = $this->modeloUsuario->contarPorRol("docente");
        $totalCoordinadores = $this->modeloUsuario->contarPorRol("coordinador");
        $totalUsuariosAprendiz = $this->modeloUsuario->contarPorRol("aprendiz");

        // Los 5 aprendices más recientes, para una vista rápida sin ir al listado completo.
        $aprendicesRecientes = $this->modeloAprendiz->obtenerRecientes(5);

        // Se muestra la vista, con todas las variables anteriores ya disponibles.
        require __DIR__ . "/../views/dashboard/coordinador.php";
    }

    // ------------------------------------------------------------------
    // Dashboard del DOCENTE: enfocado en la gestión de aprendices.
    // ------------------------------------------------------------------
    private function mostrarDashboardDocente()
    {
        // El docente ve el total de aprendices y los más recientes, pero
        // no estadísticas de usuarios (eso es exclusivo del coordinador).
        $totalAprendices     = $this->modeloAprendiz->contarTotal();
        $aprendicesRecientes = $this->modeloAprendiz->obtenerRecientes(5);

        require __DIR__ . "/../views/dashboard/docente.php";
    }

    // ------------------------------------------------------------------
    // Dashboard del APRENDIZ: solo su propia información, no la de todos.
    // ------------------------------------------------------------------
    private function mostrarDashboardAprendiz()
    {
        // Se obtiene el id de aprendiz vinculado a esta cuenta (guardado en sesión).
        $aprendizId = Auth::obtenerAprendizId();

        // Si la cuenta todavía no fue vinculada a un registro de aprendiz
        // (por ejemplo, el coordinador no lo ha asociado todavía), se le
        // avisa en vez de mostrar datos vacíos o un error.
        $miInformacion = $aprendizId ? $this->modeloAprendiz->obtenerPorId($aprendizId) : null;

        require __DIR__ . "/../views/dashboard/aprendiz.php";
    }
}
