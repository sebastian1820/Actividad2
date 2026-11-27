<!DOCTYPE html>
<!-- ============================================================================
     views/usuarios/formulario_editar.php
     ----------------------------------------------------------------------------
     VISTA exclusiva del rol "coordinador". Recibe $errores, $datos y
     $aprendicesSinVincular desde UsuarioController. La contraseña es
     OPCIONAL aquí: si se deja en blanco, se conserva la actual.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar cuenta de usuario</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; ?>

  <h2>Editar cuenta de usuario</h2>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="index.php?accion=usuarios_actualizar" method="POST" class="w-75">

    <!-- El id viaja oculto para que el Controlador sepa cuál fila actualizar -->
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($datos['id']); ?>">

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

    <!-- Bloque de vinculación, solo relevante cuando el rol es "aprendiz" -->
    <div id="bloqueAprendiz" class="border rounded p-3 mb-3 bg-light">
      <label class="form-label">Aprendiz vinculado:</label>
      <select name="aprendiz_existente_id" id="aprendiz_existente_id" class="form-select">
        <option value="">-- Ninguno --</option>
        <?php foreach ($aprendicesSinVincular as $a): ?>
          <option value="<?php echo $a['id']; ?>"
            <?php echo ((string) $datos["aprendiz_existente_id"] === (string) $a["id"]) ? "selected" : ""; ?>>
            <?php echo htmlspecialchars($a["nombre"] . " (Ficha " . $a["ficha"] . ")"); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Nueva contraseña:</label>
        <input type="password" name="contrasena" class="form-control" minlength="6"
               placeholder="Dejar en blanco para no cambiarla">
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">Confirmar nueva contraseña:</label>
        <input type="password" name="confirmar_contrasena" class="form-control" minlength="6">
      </div>
    </div>

    <button type="submit" class="btn btn-success">Guardar cambios</button>
    <a href="index.php?accion=usuarios" class="btn btn-secondary">Cancelar</a>
  </form>

  <script>
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
