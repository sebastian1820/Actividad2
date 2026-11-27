<!DOCTYPE html>
<!-- ============================================================================
     views/notas/mis_notas.php
     ----------------------------------------------------------------------------
     VISTA exclusiva del rol "aprendiz". Recibe $notas desde
     NotaController::misNotas() — SIEMPRE las de su propia cuenta, nunca las
     de otro aprendiz (el Controlador usa Auth::obtenerAprendizId(), no un
     parámetro de la URL, así que no hay forma de "ver las notas de otro"
     cambiando el enlace).
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis notas</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <h2>Mis notas</h2>

  <?php if (!$vinculado): ?>
    <div class="alert alert-warning">
      Tu cuenta todavía no está vinculada a un registro de aprendiz, así que no
      hay notas para mostrar. Contacta a tu docente o coordinador para que la asocien.
    </div>
  <?php else: ?>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Asignatura</th>
        <th>Nota</th>
        <th>Registrada por</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($notas as $n): ?>
        <tr>
          <td><?php echo htmlspecialchars($n["asignatura"]); ?></td>
          <td>
            <?php $colorNota = $n["nota"] >= 3.0 ? "bg-success" : "bg-danger"; ?>
            <span class="badge <?php echo $colorNota; ?>"><?php echo htmlspecialchars($n["nota"]); ?></span>
          </td>
          <td><?php echo htmlspecialchars($n["docente_nombre"] ?? "—"); ?></td>
          <td><?php echo htmlspecialchars($n["fecha_registro"]); ?></td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($notas)): ?>
        <tr><td colspan="4" class="text-center">Todavía no tienes notas registradas.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <p class="text-muted small">Solo tu docente o coordinador pueden registrar o modificar tus notas.</p>

  <?php endif; ?>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
