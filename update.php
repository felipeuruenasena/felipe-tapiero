<?php
include 'db.php';
$id = intval($_GET['id'] ?? 0);
$mensaje = '';
if(!$id) { header('Location: index.php'); exit; }
$comp = $conn->query("SELECT c.*, p.documento AS doc, p.nombre AS proveedor, p.email AS email, pr.codigo AS codigo_prod, pr.nombre AS nombre_prod, pr.marca AS marca, pr.valor_unitario AS valor_unitario FROM compras c JOIN proveedores p ON c.proveedor_id=p.id JOIN productos pr ON c.producto_id=pr.id WHERE c.id=$id")->fetch_assoc();

if(isset($_POST['actualizar'])){
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $cantidad = intval($_POST['cantidad']);
    $valor_unitario = floatval($_POST['valor_unitario']);
    $valor_total = $cantidad * $valor_unitario;

    // update product unit value in productos table
    $codigo = $conn->real_escape_string($_POST['codigo']);
    $conn->query("UPDATE productos SET valor_unitario=$valor_unitario, nombre='".$conn->real_escape_string($_POST['nombre_producto'])."', marca='".$conn->real_escape_string($_POST['marca'])."' WHERE codigo='$codigo'");
    $conn->query("UPDATE compras SET fecha_compra='$fecha', cantidad=$cantidad, valor_total=$valor_total WHERE id=$id");
    $mensaje = '✅ Compra actualizada';
    // refresh data
    $comp = $conn->query("SELECT c.*, p.documento AS doc, p.nombre AS proveedor, p.email AS email, pr.codigo AS codigo_prod, pr.nombre AS nombre_prod, pr.marca AS marca, pr.valor_unitario AS valor_unitario FROM compras c JOIN proveedores p ON c.proveedor_id=p.id JOIN productos pr ON c.producto_id=pr.id WHERE c.id=$id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar Compra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body{background:#0b1220;color:#e6eef8}.card{background:#0f1724}</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container my-4">
  <div class="card p-4 shadow">
    <h4 class="mb-3">✏️ Editar Compra #<?= $id ?></h4>
    <?php if($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
    <form method="POST" class="row g-3">
      <div class="col-md-3"><label class="form-label">Documento</label><input class="form-control" value="<?= htmlspecialchars($comp['doc']) ?>" readonly></div>
      <div class="col-md-3"><label class="form-label">Proveedor</label><input class="form-control" value="<?= htmlspecialchars($comp['proveedor']) ?>" readonly></div>
      <div class="col-md-3"><label class="form-label">Producto (Código)</label><input name="codigo" class="form-control" value="<?= htmlspecialchars($comp['codigo_prod']) ?>" required></div>
      <div class="col-md-3"><label class="form-label">Nombre Producto</label><input name="nombre_producto" class="form-control" value="<?= htmlspecialchars($comp['nombre_prod']) ?>" required></div>

      <div class="col-md-3"><label class="form-label">Marca</label><input name="marca" class="form-control" value="<?= htmlspecialchars($comp['marca']) ?>" required></div>
      <div class="col-md-3"><label class="form-label">Valor unitario</label><input type="number" step="0.01" name="valor_unitario" class="form-control" value="<?= number_format($comp['valor_unitario'],2,'.','') ?>" required></div>
      <div class="col-md-3"><label class="form-label">Fecha</label><input type="date" name="fecha" class="form-control" value="<?= $comp['fecha_compra'] ?>" required></div>
      <div class="col-md-3"><label class="form-label">Cantidad</label><input type="number" name="cantidad" class="form-control" value="<?= $comp['cantidad'] ?>" required></div>

      <div class="col-12">
        <button class="btn btn-warning btn-icon"><i class="bi bi-pencil-square"></i> Actualizar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
