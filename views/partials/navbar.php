<!-- ============================================================================
     views/partials/navbar.php
     ----------------------------------------------------------------------------
     PARTIAL (fragmento de Vista reutilizable). Antes, cada vista repetía el
     mismo bloque HTML de "Hola, nombre / rol / cerrar sesión". Aquí se extrae
     una sola vez y las demás vistas lo incluyen con require. Es una práctica
     típica dentro de la capa de Vista en MVC: evitar duplicar HTML idéntico
     entre pantallas distintas ("no te repitas" / DRY).
     Este archivo espera que la clase Auth ya esté cargada y la sesión ya
     iniciada (lo hace el front controller antes de llegar a cualquier vista).
     ============================================================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <!-- Saludo con el nombre y el rol de quien inició sesión -->
    <span>Hola, <strong><?php echo htmlspecialchars(Auth::obtenerNombre()); ?></strong></span>
    <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars(Auth::obtenerRol()); ?></span>
  </div>

  <!-- Enlaces de navegación + botón de salir, todos en la misma barra -->
  <div class="d-flex gap-2">
    <a href="index.php?accion=dashboard" class="btn btn-outline-primary btn-sm">Dashboard</a>
    <a href="index.php?accion=listar" class="btn btn-outline-secondary btn-sm">Listado de aprendices</a>

    <?php // El aprendiz consulta sus propias notas desde aquí también ?>
    <?php if (Auth::obtenerRol() === "aprendiz"): ?>
      <a href="index.php?accion=mis_notas" class="btn btn-outline-info btn-sm">Mis notas</a>
    <?php endif; ?>

    <?php // Gestionar usuarios (crear docentes, editar, eliminar) es exclusivo del coordinador ?>
    <?php if (Auth::obtenerRol() === "coordinador"): ?>
      <a href="index.php?accion=usuarios" class="btn btn-outline-dark btn-sm">Gestionar usuarios</a>
    <?php endif; ?>

    <a href="index.php?accion=logout" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
  </div>
</div>
