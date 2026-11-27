<!DOCTYPE html>
<!-- ============================================================================
     views/auth/registro.php
     ----------------------------------------------------------------------------
     VISTA del formulario de auto-registro. Recibe $errores y $datos (los
     valores ya escritos, para no perderlos si el formulario se recarga por
     un error de validación) desde AuthController. Solo permite elegir entre
     rol "aprendiz" o "coordinador"; el rol "docente" NUNCA aparece aquí a
     propósito (esas cuentas las crea el coordinador desde su CRUD interno).
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear cuenta</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5" style="max-width: 480px;">

  <h2 class="mb-4 text-center">Crear cuenta</h2>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="index.php?accion=registrar" method="POST" id="formRegistro">

    <div class="mb-3">
      <label class="form-label">Nombre completo:</label>
      <input type="text" name="nombre" class="form-control"
             value="<?php echo htmlspecialchars($datos["nombre"]); ?>" required autofocus>
    </div>

    <div class="mb-3">
      <label class="form-label">Usuario:</label>
      <input type="text" name="usuario" class="form-control"
             value="<?php echo htmlspecialchars($datos["usuario"]); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Correo:</label>
      <input type="email" name="correo" class="form-control"
             value="<?php echo htmlspecialchars($datos["correo"]); ?>" required>
    </div>

    <!-- Solo dos roles disponibles en el registro público -->
    <div class="mb-3">
      <label class="form-label">Voy a registrarme como:</label>
      <select name="rol" id="rol" class="form-select" required>
        <option value="aprendiz" <?php echo $datos["rol"] === "aprendiz" ? "selected" : ""; ?>>Aprendiz</option>
        <option value="coordinador" <?php echo $datos["rol"] === "coordinador" ? "selected" : ""; ?>>Coordinador</option>
      </select>
      <div class="form-text">
        ¿Eres docente? Pide a un coordinador que cree tu cuenta desde el sistema.
      </div>
    </div>

    <!-- Este campo solo se necesita (y solo se muestra) si el rol elegido es "aprendiz" -->
    <div class="mb-3" id="grupoFicha">
      <label class="form-label">Ficha:</label>
      <input type="text" name="ficha" id="ficha" class="form-control"
             value="<?php echo htmlspecialchars($datos["ficha"]); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Contraseña:</label>
      <input type="password" name="contrasena" class="form-control" minlength="6" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirmar contraseña:</label>
      <input type="password" name="confirmar_contrasena" class="form-control" minlength="6" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
  </form>

  <p class="text-center mt-3">
    <a href="index.php?accion=login">Ya tengo cuenta, iniciar sesión</a>
  </p>

  <script>
    // Muestra/oculta y activa/desactiva "required" en el campo Ficha según
    // el rol elegido, ya que solo es obligatorio para el rol "aprendiz".
    const selectRol   = document.getElementById("rol");
    const grupoFicha  = document.getElementById("grupoFicha");
    const campoFicha  = document.getElementById("ficha");

    function actualizarFicha() {
      const esAprendiz = selectRol.value === "aprendiz";
      grupoFicha.style.display = esAprendiz ? "block" : "none";
      campoFicha.required = esAprendiz;
    }

    selectRol.addEventListener("change", actualizarFicha);
    actualizarFicha(); // Estado inicial al cargar la página
  </script>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
