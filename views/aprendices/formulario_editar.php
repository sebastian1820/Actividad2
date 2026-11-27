<!DOCTYPE html>
<!-- ============================================================================
     views/aprendices/formulario_editar.php
     ----------------------------------------------------------------------------
     VISTA: formulario de edición. Recibe la variable $aprendiz (un arreglo
     asociativo con id, nombre y ficha) que el Controlador obtuvo del Modelo.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Aprendiz</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; // Barra superior reutilizable ?>

  <h2>Editar aprendiz</h2>

  <?php if ($aprendiz): // Solo se muestra el formulario si el aprendiz existe ?>
    <!-- El formulario envía los cambios por POST a la acción "actualizar" -->
    <form action="index.php?accion=actualizar" method="POST" class="w-50">

      <!-- Campo oculto: viaja el id para saber qué registro actualizar,
           pero el usuario no lo ve ni lo edita -->
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($aprendiz['id']); ?>">

      <div class="mb-3">
        <label class="form-label">Nombre:</label>
        <!-- "value" precarga el campo con el nombre actual del aprendiz -->
        <input type="text" name="nombre" class="form-control"
               value="<?php echo htmlspecialchars($aprendiz['nombre']); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Ficha:</label>
        <input type="text" name="ficha" class="form-control"
               value="<?php echo htmlspecialchars($aprendiz['ficha']); ?>" required>
      </div>

      <button type="submit" class="btn btn-success">Actualizar</button>
      <a href="index.php?accion=listar" class="btn btn-secondary">Cancelar</a>
    </form>
  <?php else: // Si no se encontró el aprendiz con ese id ?>
    <p class="text-danger">No se encontró el aprendiz solicitado.</p>
    <a href="index.php?accion=listar" class="btn btn-secondary">Volver al listado</a>
  <?php endif; ?>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
