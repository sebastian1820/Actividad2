<!DOCTYPE html>
<!-- ============================================================================
     views/dashboard/coordinador.php
     ----------------------------------------------------------------------------
     VISTA exclusiva del rol "coordinador". Recibe $totalAprendices,
     $totalDocentes, $totalCoordinadores, $totalUsuariosAprendiz y
     $aprendicesRecientes desde DashboardController. Solo presenta HTML:
     ninguna consulta a la base de datos ocurre aquí.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Coordinador</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <h2>Panel del coordinador</h2>
  <p class="text-muted">Vista general de todo el sistema: aprendices y usuarios registrados.</p>

  <!-- Tarjetas de estadísticas: una por cada dato relevante para el coordinador -->
  <div class="row g-3 my-2">

    <div class="col-md-3">
      <div class="card text-bg-primary">
        <div class="card-body">
          <h6 class="card-title">Total aprendices</h6>
          <p class="display-6 mb-0"><?php echo (int) $totalAprendices; ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-bg-success">
        <div class="card-body">
          <h6 class="card-title">Docentes</h6>
          <p class="display-6 mb-0"><?php echo (int) $totalDocentes; ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-bg-warning">
        <div class="card-body">
          <h6 class="card-title">Coordinadores</h6>
          <p class="display-6 mb-0"><?php echo (int) $totalCoordinadores; ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-bg-secondary">
        <div class="card-body">
          <h6 class="card-title">Usuarios aprendiz</h6>
          <p class="display-6 mb-0"><?php echo (int) $totalUsuariosAprendiz; ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Accesos rápidos a las acciones que el coordinador puede hacer -->
  <div class="my-3 d-flex gap-2 flex-wrap">
    <a href="index.php?accion=crear" class="btn btn-primary">+ Registrar aprendiz</a>
    <a href="index.php?accion=usuarios_crear" class="btn btn-dark">+ Crear docente / usuario</a>
    <a href="index.php?accion=usuarios" class="btn btn-outline-dark">Gestionar usuarios</a>
    <a href="index.php?accion=listar" class="btn btn-outline-secondary">Ver listado completo</a>
  </div>

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
            <a href="index.php?accion=notas&aprendiz_id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-info text-white">Notas</a>
            <a href="index.php?accion=editar&id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
            <a href="index.php?accion=eliminar&id=<?php echo $fila['id']; ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('¿Seguro que deseas eliminar este aprendiz?');">Eliminar</a>
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
