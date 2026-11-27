<!DOCTYPE html>
<!-- ============================================================================
     views/auth/recuperar.php
     ----------------------------------------------------------------------------
     VISTA: paso 1 de la recuperación de contraseña. Solo pide el correo.
     Recibe $errores desde AuthController (puede venir vacío).
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar contraseña</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5" style="max-width: 420px;">

  <h2 class="mb-3 text-center">Recuperar contraseña</h2>
  <p class="text-muted text-center">
    Escribe el correo con el que te registraste. Si existe una cuenta asociada,
    te enviaremos un enlace para crear una nueva contraseña.
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

  <!-- Envía el correo por POST a la acción que genera el token y envía el mensaje -->
  <form action="index.php?accion=enviar_recuperacion" method="POST">
    <div class="mb-3">
      <label class="form-label">Correo electrónico:</label>
      <input type="email" name="correo" class="form-control" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary w-100">Enviar enlace de recuperación</button>
  </form>

  <p class="text-center mt-3">
    <a href="index.php?accion=login">Volver al inicio de sesión</a>
  </p>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
