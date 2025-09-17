<?php
include 'db.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mensaje = '';
if(isset($_POST['importar'])){
    if(!isset($_FILES['excel']) || $_FILES['excel']['error']!=0){
        $mensaje = '❌ Error al subir el archivo.';
    } else {
        $tmp = $_FILES['excel']['tmp_name'];
        try {
            $spreadsheet = IOFactory::load($tmp);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            foreach($rows as $i => $row){
                if($i==0) continue; // encabezado
                $documento = $conn->real_escape_string($row[0] ?? '');
                $nombre_proveedor = $conn->real_escape_string($row[1] ?? '');
                $email = $conn->real_escape_string($row[2] ?? '');
                $codigo = $conn->real_escape_string($row[3] ?? '');
                $nombre_producto = $conn->real_escape_string($row[4] ?? '');
                $marca = $conn->real_escape_string($row[5] ?? '');
                $valor_unitario = floatval($row[6] ?? 0);
                $fecha = $conn->real_escape_string($row[7] ?? '');
                $cantidad = intval($row[8] ?? 0);
                $valor_total = $cantidad * $valor_unitario;

                if(!$documento || !$codigo) continue;

                $conn->query("INSERT INTO proveedores (documento,nombre,email) VALUES ('$documento','$nombre_proveedor','$email') ON DUPLICATE KEY UPDATE nombre='$nombre_proveedor', email='$email'");
                $proveedor_id = $conn->insert_id ?: $conn->query("SELECT id FROM proveedores WHERE documento='$documento'")->fetch_assoc()['id'];

                $conn->query("INSERT INTO productos (codigo,nombre,marca,valor_unitario) VALUES ('$codigo','$nombre_producto','$marca',$valor_unitario) ON DUPLICATE KEY UPDATE nombre='$nombre_producto', marca='$marca', valor_unitario=$valor_unitario");
                $producto_id = $conn->insert_id ?: $conn->query("SELECT id FROM productos WHERE codigo='$codigo'")->fetch_assoc()['id'];

                $conn->query("INSERT INTO compras (proveedor_id, producto_id, fecha_compra, cantidad, valor_total) VALUES ($proveedor_id,$producto_id,'$fecha',$cantidad,$valor_total)");
            }
            $mensaje = '✅ Importación finalizada';
        } catch(Exception $e){
            $mensaje = '❌ Error leyendo Excel: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Importar Excel</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body{background:#0b1220;color:#e6eef8}.card{background:#0f1724}</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container my-4">
  <div class="card p-4 shadow">
    <h4 class="mb-3"><i class="bi bi-file-earmark-arrow-up"></i> Importar Compras desde Excel (.xlsx)</h4>
    <?php if($mensaje): ?><div class="alert alert-info"><?= $mensaje ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Archivo .xlsx (Primera fila = encabezado)</label>
        <input type="file" name="excel" accept=".xlsx" class="form-control" required>
      </div>
      <div class="mb-3">
        <small>Formato esperado de columnas: documento, nombre_proveedor, email, codigo, nombre_producto, marca, valor_unitario, fecha (YYYY-MM-DD), cantidad</small>
      </div>
      <button class="btn btn-primary btn-icon" name="importar"><i class="bi bi-cloud-upload"></i> Importar</button>
      <a href="index.php" class="btn btn-secondary">Volver</a>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
