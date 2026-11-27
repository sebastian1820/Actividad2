<!DOCTYPE html>
<!-- ============================================================================
     views/usuarios/formulario_crear.php
     ----------------------------------------------------------------------------
     VISTA exclusiva del rol "coordinador". Recibe $errores, $datos y
     $aprendicesSinVincular desde UsuarioController. A diferencia del
     registro público, aquí SÍ aparece el rol "docente" en el selector,
     porque es el coordinador quien da de alta a los docentes.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva cuenta de usuario</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <h2>Nueva cuenta de usuario</h2>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="index.php?accion=usuarios_guardar" method="POST" class="w-75" id="formCrearUsuario">

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Nombre completo:</label>
        <input type="text" name="nombre" class="form-control"
               value="<?php echo htmlspecialchars($datos["nombre"]); ?>" required autofocus>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">Usuario:</label>
        <input type="text" name="usuario" class="form-control"
               value="<?php echo htmlspecialchars($datos["usuario"]); ?>" required>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Correo:</label>
        <input type="email" name="correo" class="form-control"
               value="<?php echo htmlspecialchars($datos["correo"]); ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">Rol:</label>
        <select name="rol" id="rol" class="form-select" required>
          <option value="aprendiz" <?php echo $datos["rol"] === "aprendiz" ? "selected" : ""; ?>>Aprendiz</option>
          <option value="docente" <?php echo $datos["rol"] === "docente" ? "selected" : ""; ?>>Docente</option>
          <option value="coordinador" <?php echo $datos["rol"] === "coordinador" ? "selected" : ""; ?>>Coordinador</option>
        </select>
      </div>
    </div>

    <!-- Bloque que solo aplica cuando el rol elegido es "aprendiz" -->
    <div id="bloqueAprendiz" class="border rounded p-3 mb-3 bg-light">
      <p class="mb-2 fw-semibold">Vinculación con la tabla de aprendices:</p>

      <div class="mb-2">
        <label class="form-label">Opción A · Crear una ficha nueva:</label>
        <input type="text" name="ficha" id="ficha" class="form-control"
               placeholder="Número de ficha" value="<?php echo htmlspecialchars($datos["ficha"]); ?>">
      </div>

      <div class="mb-0">
        <label class="form-label">Opción B · Vincular a un aprendiz ya existente (sin cuenta):</label>
        <select name="aprendiz_existente_id" id="aprendiz_existente_id" class="form-select">
          <option value="">-- Ninguno --</option>
          <?php foreach ($aprendicesSinVincular as $a): ?>
            <option value="<?php echo $a['id']; ?>">
              <?php echo htmlspecialchars($a["nombre"] . " (Ficha " . $a["ficha"] . ")"); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-text">Usa solo una de las dos opciones.</div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Contraseña:</label>
        <input type="password" name="contrasena" class="form-control" minlength="6" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">Confirmar contraseña:</label>
        <input type="password" name="confirmar_contrasena" class="form-control" minlength="6" required>
      </div>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="index.php?accion=usuarios" class="btn btn-secondary">Cancelar</a>
  </form>

  <script>
    // Solo se ve el bloque de vinculación con "aprendices" cuando el rol
    // seleccionado es justamente "aprendiz".
    const selectRol      = document.getElementById("rol");
    const bloqueAprendiz = document.getElementById("bloqueAprendiz");

    function actualizarBloque() {
      bloqueAprendiz.style.display = selectRol.value === "aprendiz" ? "block" : "none";
    }

    selectRol.addEventListener("change", actualizarBloque);
    actualizarBloque();
  </script>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
