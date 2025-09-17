<?php
require 'conexion.php';
$desde = !empty($_GET['desde']) ? $_GET['desde'] : null;
$hasta = !empty($_GET['hasta']) ? $_GET['hasta'] : null;
$where = ''; $params = [];
if ($desde) { $where .= " AND c.fecha_compra >= ?"; $params[] = $desde . " 00:00:00"; }
if ($hasta) { $where .= " AND c.fecha_compra <= ?"; $params[] = $hasta . " 23:59:59"; }

$sql = "SELECT
  c.id as compra_id,
  p.documento AS proveedor_documento,
  p.nombre AS proveedor_nombre,
  p.email AS proveedor_email,
  c.fecha_compra,
  pr.codigo AS codigo_producto,
  pr.nombre AS nombre_producto,
  pr.marca AS marca,
  cd.cantidad,
  cd.valor_unitario,
  cd.valor_total
FROM compras c
JOIN proveedores p ON c.proveedor_id = p.id
JOIN compras_detalle cd ON cd.compra_id = c.id
JOIN productos pr ON cd.producto_id = pr.id
WHERE 1=1 $where
ORDER BY c.fecha_compra DESC LIMIT 5000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reportes - MiElementos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Reporte de Compras</h4>
    <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver</a>
  </div>

  <div class="card p-3 mb-3">
    <form class="row g-2" method="get">
      <div class="col-auto">
        <label class="form-label small-muted">Desde</label>
        <input type="date" name="desde" class="form-control form-control-sm" value="<?= htmlspecialchars($desde) ?>">
      </div>
      <div class="col-auto">
        <label class="form-label small-muted">Hasta</label>
        <input type="date" name="hasta" class="form-control form-control-sm" value="<?= htmlspecialchars($hasta) ?>">
      </div>
      <div class="col-auto align-self-end">
        <button class="btn btn-primary btn-sm">Filtrar</button>
      </div>
      <div class="col-auto align-self-end">
        <a href="export_csv.php?<?= http_build_query(['desde'=>$desde,'hasta'=>$hasta]) ?>" class="btn btn-outline-success btn-sm">Exportar CSV</a>
      </div>
    </form>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr>
            <th>Documento</th>
            <th>Proveedor</th>
            <th>Email</th>
            <th>Fecha Compra</th>
            <th>Código</th>
            <th>Producto</th>
            <th>Marca</th>
            <th class="text-end">Cantidad</th>
            <th class="text-end">Valor Unit.</th>
            <th class="text-end">Valor Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['proveedor_documento']) ?></td>
            <td><?= htmlspecialchars($r['proveedor_nombre']) ?></td>
            <td><?= htmlspecialchars($r['proveedor_email']) ?></td>
            <td><?= htmlspecialchars($r['fecha_compra']) ?></td>
            <td><?= htmlspecialchars($r['codigo_producto']) ?></td>
            <td><?= htmlspecialchars($r['nombre_producto']) ?></td>
            <td><?= htmlspecialchars($r['marca']) ?></td>
            <td class="text-end"><?= number_format($r['cantidad'], 2) ?></td>
            <td class="text-end"><?= number_format($r['valor_unitario'], 2) ?></td>
            <td class="text-end"><?= number_format($r['valor_total'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if(empty($rows)): ?>
      <div class="text-muted small">No se encontraron compras con los filtros aplicados.</div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
