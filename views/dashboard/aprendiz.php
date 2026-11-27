<!DOCTYPE html>
<!-- ============================================================================
     views/dashboard/aprendiz.php
     ----------------------------------------------------------------------------
     VISTA exclusiva del rol "aprendiz". Recibe $miInformacion desde
     DashboardController: será un arreglo con sus propios datos (id, nombre,
     ficha) si su cuenta está vinculada a un registro de "aprendices", o null
     si todavía no ha sido vinculada. A diferencia de los otros dos
     dashboards, este NO recibe la lista completa de aprendices: por diseño,
     un aprendiz solo puede ver su propia información, nunca la de los demás.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi información</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <h2>Mi información</h2>

  <?php if ($miInformacion): // La cuenta SÍ está vinculada a un registro de aprendiz ?>
    <div class="card" style="max-width: 400px;">
      <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($miInformacion["nombre"]); ?></h5>
        <p class="card-text mb-1">
          <strong>Ficha:</strong> <?php echo htmlspecialchars($miInformacion["ficha"]); ?>
        </p>
        <p class="card-text text-muted small mb-0">
          ID de registro: <?php echo htmlspecialchars($miInformacion["id"]); ?>
        </p>
      </div>
    </div>
    <p class="text-muted small mt-3">
      Este es tu registro como aprendiz. Solo tu docente o coordinador pueden modificarlo.
    </p>
    <a href="index.php?accion=mis_notas" class="btn btn-primary">Ver mis notas</a>
  <?php else: // La cuenta todavía no fue vinculada a ningún registro ?>
    <div class="alert alert-warning">
      Tu cuenta todavía no está vinculada a un registro de aprendiz.
      Contacta a tu docente o coordinador para que la asocien.
    </div>
  <?php endif; ?>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
