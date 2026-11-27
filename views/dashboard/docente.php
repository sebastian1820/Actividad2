<!DOCTYPE html>
<!-- ============================================================================
     views/dashboard/docente.php
     ----------------------------------------------------------------------------
     VISTA exclusiva del rol "docente". Recibe $totalAprendices y
     $aprendicesRecientes desde DashboardController. A diferencia del
     coordinador, el docente no ve estadísticas de usuarios ni el botón de
     eliminar (esa acción sigue reservada solo al coordinador en el Controlador).
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Docente</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <h2>Panel del docente</h2>
  <p class="text-muted">Gestiona el registro de tus aprendices.</p>

  <!-- Una sola tarjeta destacada: lo único que el docente necesita ver de un vistazo -->
  <div class="card text-bg-primary mb-3" style="max-width: 260px;">
    <div class="card-body">
      <h6 class="card-title">Total aprendices</h6>
      <p class="display-6 mb-0"><?php echo (int) $totalAprendices; ?></p>
    </div>
  </div>

  <!-- Accesos rápidos a las acciones que el docente puede hacer -->
  <div class="my-3 d-flex gap-2 flex-wrap">
    <a href="index.php?accion=listar" class="btn btn-primary">Ver aprendices y gestionar notas</a>
  </div>
  <p class="text-muted small">
    El registro inicial de un aprendiz nuevo lo hace el coordinador. Desde el
    listado puedes ver, editar, subir notas e inactivar por retiro a un aprendiz.
  </p>

  <h5 class="mt-4">Últimos aprendices registrados</h5>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Ficha</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($aprendicesRecientes as $fila): ?>
        <tr>
          <td><?php echo htmlspecialchars($fila["id"]); ?></td>
          <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
          <td><?php echo htmlspecialchars($fila["ficha"]); ?></td>
          <td>
            <?php if ($fila["estado"] === "activo"): ?>
              <span class="badge bg-success">Activo</span>
            <?php else: ?>
              <span class="badge bg-secondary">Retirado</span>
            <?php endif; ?>
          </td>
          <td>
            <!-- El docente puede ver notas y editar, pero no eliminar (eso es solo del coordinador) -->
            <a href="index.php?accion=notas&aprendiz_id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-info text-white">Notas</a>
            <a href="index.php?accion=editar&id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($aprendicesRecientes)): ?>
        <tr>
          <td colspan="5" class="text-center">Todavía no hay aprendices registrados.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
