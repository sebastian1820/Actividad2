<!DOCTYPE html>
<!-- ============================================================================
     views/aprendices/formulario_crear.php
     ----------------------------------------------------------------------------
     VISTA: formulario vacío para registrar un aprendiz nuevo.
     Solo presenta HTML; el envío (POST) lo procesará el Controlador a través
     de index.php?accion=guardar.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Aprendiz</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; // Barra superior reutilizable ?>

  <h2>Registrar nuevo aprendiz</h2>

  <!-- El formulario envía los datos por POST a la acción "guardar" del front controller -->
  <form action="index.php?accion=guardar" method="POST" class="w-50">

    <div class="mb-3">
      <label class="form-label">Nombre:</label>
      <!-- "required" obliga a llenar el campo antes de enviar el formulario -->
      <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Ficha:</label>
      <input type="text" name="ficha" class="form-control" required>
    </div>

    <!-- Al hacer clic, el navegador envía el formulario a "action" -->
    <button type="submit" class="btn btn-success">Guardar</button>

    <!-- Enlace para volver al listado sin guardar nada -->
    <a href="index.php?accion=listar" class="btn btn-secondary">Cancelar</a>
  </form>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
