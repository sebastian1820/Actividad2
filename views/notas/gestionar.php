<!DOCTYPE html>
<!-- ============================================================================
     views/notas/gestionar.php
     ----------------------------------------------------------------------------
     VISTA para docente y coordinador. Recibe $aprendiz, $notas y $errores
     desde NotaController::gestionar(). Permite ver el historial de notas de
     UN aprendiz, subir una nota nueva y eliminar una existente.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Notas de <?php echo htmlspecialchars($aprendiz["nombre"]); ?></title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <a href="index.php?accion=listar" class="btn btn-sm btn-outline-secondary mb-3">&larr; Volver al listado</a>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0">Notas de <?php echo htmlspecialchars($aprendiz["nombre"]); ?></h2>
      <p class="text-muted mb-0">
        Ficha <?php echo htmlspecialchars($aprendiz["ficha"]); ?>
        &middot;
        <?php if ($aprendiz["estado"] === "activo"): ?>
          <span class="badge bg-success">Activo</span>
        <?php else: ?>
          <span class="badge bg-secondary">Retirado</span>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Formulario para subir una nota nueva -->
  <div class="card mb-4" style="max-width: 600px;">
    <div class="card-body">
      <h5 class="card-title">Subir nueva nota</h5>
      <form action="index.php?accion=notas_guardar" method="POST" class="row g-2 align-items-end">
        <input type="hidden" name="aprendiz_id" value="<?php echo htmlspecialchars($aprendiz['id']); ?>">

        <div class="col-md-6">
          <label class="form-label">Asignatura / Actividad:</label>
          <input type="text" name="asignatura" class="form-control" placeholder="Ej: Base de datos" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Nota (0.0 a 5.0):</label>
          <input type="number" name="nota" class="form-control" step="0.1" min="0" max="5" required>
        </div>

        <div class="col-md-3">
          <button type="submit" class="btn btn-success w-100">Guardar nota</button>
        </div>
      </form>
    </div>
  </div>

  <h5>Historial de notas</h5>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Asignatura</th>
        <th>Nota</th>
        <th>Registrada por</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($notas as $n): ?>
        <tr>
          <td><?php echo htmlspecialchars($n["asignatura"]); ?></td>
          <td>
            <?php
              // Color según si la nota es aprobatoria (>= 3.0) o no, solo
              // como referencia visual rápida (escala típica del SENA).
              $colorNota = $n["nota"] >= 3.0 ? "bg-success" : "bg-danger";
            ?>
            <span class="badge <?php echo $colorNota; ?>"><?php echo htmlspecialchars($n["nota"]); ?></span>
          </td>
          <td><?php echo htmlspecialchars($n["docente_nombre"] ?? "—"); ?></td>
          <td><?php echo htmlspecialchars($n["fecha_registro"]); ?></td>
          <td>
            <a href="index.php?accion=notas_eliminar&id=<?php echo $n['id']; ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('¿Eliminar esta nota?');">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($notas)): ?>
        <tr><td colspan="5" class="text-center">Todavía no hay notas registradas para este aprendiz.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
