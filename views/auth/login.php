<!DOCTYPE html>
<!-- ============================================================================
     views/auth/login.php
     ----------------------------------------------------------------------------
     VISTA del formulario de inicio de sesión. Recibe la variable $errores
     (un arreglo de mensajes) que el Controlador ya calculó. Esta vista solo
     los recorre y los muestra; no decide ella misma si hay error o no.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5" style="max-width: 420px;">

  <h2 class="mb-4 text-center">Iniciar sesión</h2>

  <?php if (!empty($mensajeExito)): // Mensaje de éxito, ej. tras restablecer la contraseña ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($mensajeExito); ?></div>
  <?php endif; ?>

  <?php if (!empty($errores)): // Solo se muestra el bloque si hay al menos un error ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): // Se recorre cada mensaje de error ?>
          <!-- htmlspecialchars evita que un mensaje con caracteres especiales rompa el HTML -->
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- El formulario envía usuario y contraseña por POST a la acción "autenticar" -->
  <form action="index.php?accion=autenticar" method="POST">

    <div class="mb-3">
      <label class="form-label">Usuario:</label>
      <input type="text" name="usuario" class="form-control" required autofocus>
    </div>

    <div class="mb-3">
      <label class="form-label">Contraseña:</label>
      <input type="password" name="contrasena" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
  </form>

  <!-- Enlace hacia el paso 1 de la recuperación de contraseña -->
  <p class="text-center mt-3 mb-1">
    <a href="index.php?accion=recuperar">¿Olvidaste tu contraseña?</a>
  </p>

  <!-- Enlace hacia el registro público (solo aprendiz o coordinador se auto-registran) -->
  <p class="text-center mt-1">
    ¿No tienes cuenta? <a href="index.php?accion=registro">Regístrate</a>
  </p>

  <!-- Ayuda visual para la clase: recuerda los usuarios de prueba y sus roles -->
  <div class="mt-4 small text-muted">
    <p class="mb-1"><strong>Usuarios de prueba (ver sql/schema.sql):</strong></p>
    <p class="mb-0">aprendiz1 / docente1 / coordinador1</p>
  </div>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
