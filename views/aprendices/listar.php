<!DOCTYPE html>
<!-- ============================================================================
     views/aprendices/listar.php
     ----------------------------------------------------------------------------
     VISTA (la "V" de MVC). Solo presenta HTML. Recibe $aprendices desde el
     Controlador. Además, usa la clase Auth (ya cargada por el front controller)
     ÚNICAMENTE para decidir qué botones MOSTRAR U OCULTAR según el rol; la
     vista no decide si la acción está permitida, eso ya lo validó el
     Controlador — aquí solo se evita mostrar un botón que de todas formas
     el Controlador rechazaría, para no confundir al usuario.
     ============================================================================ -->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Aprendices</title>
  <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

  <?php require __DIR__ . "/../partials/navbar.php"; // Barra superior reutilizable ?>

  <h2>Aprendices registrados</h2>

  <?php
    // Se recuperan (y se borran) mensajes de "acceso denegado" guardados en
    // sesión por Auth::requerirRol() cuando alguien intentó una acción sin permiso.
    if (isset($_SESSION["error_permiso"])):
  ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars($_SESSION["error_permiso"]); ?></div>
    <?php unset($_SESSION["error_permiso"]); // Se borra para que no vuelva a aparecer al recargar ?>
  <?php endif; ?>

  <?php if (Auth::tieneRol(["coordinador"])): // Solo coordinador registra aprendices nuevos ?>
    <a href="index.php?accion=crear" class="btn btn-primary mb-3">+ Registrar nuevo aprendiz</a>
  <?php endif; ?>

  <table class="table table-bordered align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Ficha</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($aprendices as $fila): ?>
        <tr>
          <td><?php echo htmlspecialchars($fila["id"]); ?></td>
          <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
          <td><?php echo htmlspecialchars($fila["ficha"]); ?></td>
          <td>
            <?php if ($fila["estado"] === "activo"): ?>
              <span class="badge bg-success">Activo</span>
            <?php else: ?>
              <span class="badge bg-secondary">Retirado</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if (Auth::tieneRol(["docente", "coordinador"])): // Notas: docente y coordinador ?>
              <a href="index.php?accion=notas&aprendiz_id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-info text-white">Notas</a>
            <?php endif; ?>

            <?php if (Auth::tieneRol(["docente", "coordinador"])): // Editar: docente y coordinador ?>
              <a href="index.php?accion=editar&id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
            <?php endif; ?>

            <?php if (Auth::tieneRol(["docente", "coordinador"])): // Inactivar/Activar: docente y coordinador ?>
              <?php if ($fila["estado"] === "activo"): ?>
                <a href="index.php?accion=aprendices_inactivar&id=<?php echo $fila['id']; ?>"
                   class="btn btn-sm btn-outline-secondary"
                   onclick="return confirm('¿Marcar a este aprendiz como retirado?');">Inactivar por retiro</a>
              <?php else: ?>
                <a href="index.php?accion=aprendices_activar&id=<?php echo $fila['id']; ?>"
                   class="btn btn-sm btn-outline-success"
                   onclick="return confirm('¿Reactivar a este aprendiz?');">Reactivar</a>
              <?php endif; ?>
            <?php endif; ?>

            <?php if (Auth::tieneRol(["coordinador"])): // Eliminar (borrado permanente): solo coordinador ?>
              <a href="index.php?accion=eliminar&id=<?php echo $fila['id']; ?>"
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Esto BORRA el registro por completo (incluidas sus notas). ¿Seguro?');">Eliminar</a>
            <?php endif; ?>

            <?php if (!Auth::tieneRol(["docente", "coordinador"])): // Aprendiz: solo lectura ?>
              <span class="text-muted small">Solo lectura</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($aprendices)): ?>
        <tr>
          <td colspan="5" class="text-center">No hay aprendices registrados todavía.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
