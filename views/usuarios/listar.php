<!DOCTYPE html>
<!-- ============================================================================
     views/usuarios/listar.php
     ----------------------------------------------------------------------------
     VISTA exclusiva del rol "coordinador". Recibe $usuarios (arreglo con
     todas las cuentas del sistema) desde UsuarioController::listar().
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de usuarios</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Gestión de usuarios</h2>
    <a href="index.php?accion=usuarios_crear" class="btn btn-primary">+ Nueva cuenta</a>
  </div>
  <p class="text-muted">
    Aquí puedes crear, editar y eliminar cualquier cuenta del sistema, incluidas las de los docentes.
  </p>

  <table class="table table-bordered align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Usuario</th>
        <th>Correo</th>
        <th>Rol</th>
        <th>Creado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $fila): ?>
        <tr>
          <td><?php echo htmlspecialchars($fila["id"]); ?></td>
          <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
          <td><?php echo htmlspecialchars($fila["usuario"]); ?></td>
          <td><?php echo htmlspecialchars($fila["correo"]); ?></td>
          <td>
            <?php
              // Un color de insignia distinto por rol, solo para que la
              // tabla se lea más rápido de un vistazo.
              $colorRol = [
                  "coordinador" => "bg-warning",
                  "docente"     => "bg-success",
                  "aprendiz"    => "bg-secondary",
              ][$fila["rol"]] ?? "bg-dark";
            ?>
            <span class="badge <?php echo $colorRol; ?> text-uppercase"><?php echo htmlspecialchars($fila["rol"]); ?></span>
          </td>
          <td><?php echo htmlspecialchars($fila["fecha_creacion"]); ?></td>
          <td>
            <a href="index.php?accion=usuarios_editar&id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
            <?php if ((int) $fila["id"] !== (int) Auth::obtenerIdUsuario()): ?>
              <a href="index.php?accion=usuarios_eliminar&id=<?php echo $fila['id']; ?>"
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('¿Seguro que deseas eliminar esta cuenta?');">Eliminar</a>
            <?php else: ?>
              <span class="badge bg-light text-dark border">Tu cuenta</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($usuarios)): ?>
        <tr><td colspan="7" class="text-center">No hay usuarios registrados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
