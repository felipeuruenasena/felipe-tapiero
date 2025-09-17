<?php
include 'db.php';
$mensaje = '';
if(isset($_POST['guardar'])){
    $documento = $conn->real_escape_string($_POST['documento']);
    $nombre_proveedor = $conn->real_escape_string($_POST['nombre_proveedor']);
    $email = $conn->real_escape_string($_POST['email']);
    $codigo = $conn->real_escape_string($_POST['codigo']);
    $nombre_producto = $conn->real_escape_string($_POST['nombre_producto']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $valor_unitario = floatval($_POST['valor_unitario']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $cantidad = intval($_POST['cantidad']);
    $valor_total = $cantidad * $valor_unitario;

    $conn->query("INSERT INTO proveedores (documento,nombre,email) VALUES ('$documento','$nombre_proveedor','$email') ON DUPLICATE KEY UPDATE nombre='$nombre_proveedor', email='$email'");
    $proveedor_id = $conn->insert_id ?: $conn->query("SELECT id FROM proveedores WHERE documento='$documento'")->fetch_assoc()['id'];

    $conn->query("INSERT INTO productos (codigo,nombre,marca,valor_unitario) VALUES ('$codigo','$nombre_producto','$marca',$valor_unitario) ON DUPLICATE KEY UPDATE nombre='$nombre_producto', marca='$marca', valor_unitario=$valor_unitario");
    $producto_id = $conn->insert_id ?: $conn->query("SELECT id FROM productos WHERE codigo='$codigo'")->fetch_assoc()['id'];

    $conn->query("INSERT INTO compras (proveedor_id, producto_id, fecha_compra, cantidad, valor_total) VALUES ($proveedor_id,$producto_id,'$fecha',$cantidad,$valor_total)");
    $mensaje = '✅ Compra registrada';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nueva Compra - MiElementos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body{background:#e6eef8;color:#e6eef8}.card{background:#e6eef8}</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container my-4">
  <div class="card p-4 shadow">
    <h4 class="mb-3">Registrar Compra</h4>
    <?php if($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
    <form method="POST" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Documento</label>
        <input name="documento" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Nombre proveedor</label>
        <input name="nombre_proveedor" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Código producto</label>
        <input name="codigo" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Nombre producto</label>
        <input name="nombre_producto" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Marca</label>
        <input name="marca" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Valor unitario</label>
        <input type="number" step="0.01" name="valor_unitario" class="form-control" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Fecha</label>
        <input type="date" name="fecha" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Cantidad</label>
        <input type="number" name="cantidad" class="form-control" required>
      </div>

      <div class="col-12">
        <button class="btn btn-primary btn-icon"><i class="bi bi-save"></i> Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
