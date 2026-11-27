<!DOCTYPE html>
<!-- ============================================================================
     views/auth/restablecer.php
     ----------------------------------------------------------------------------
     VISTA: paso final de la recuperación de contraseña. Recibe $errores y
     $usuario (con al menos su "id" y "token" ya validados por el
     Controlador) para mostrar el formulario de nueva contraseña.
     El token viaja en un campo oculto para que el Controlador lo vuelva a
     validar al recibir el envío (nunca se confía en que "ya se validó antes").
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva contraseña</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5" style="max-width: 420px;">

  <h2 class="mb-3 text-center">Crear nueva contraseña</h2>
  <p class="text-muted text-center">
    Hola <?php echo htmlspecialchars($usuario["nombre"]); ?>, escribe tu nueva contraseña.
  </p>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Se reenvía el token junto con la nueva contraseña -->
  <form action="index.php?accion=restablecer&token=<?php echo urlencode($token); ?>" method="POST">

    <!-- Campo oculto: el Controlador lo vuelve a validar antes de guardar nada -->
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

    <div class="mb-3">
      <label class="form-label">Nueva contraseña:</label>
      <input type="password" name="contrasena" class="form-control" required minlength="6">
      <div class="form-text">Mínimo 6 caracteres.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirmar nueva contraseña:</label>
      <input type="password" name="confirmar_contrasena" class="form-control" required minlength="6">
    </div>

    <button type="submit" class="btn btn-primary w-100">Guardar nueva contraseña</button>
  </form>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
