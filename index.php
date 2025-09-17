<?php
include "db.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Compras - MiElementos</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #0b1220; color: #e6eef8; }
    .card { background-color: #0f1724; border: 1px solid rgba(255,255,255,0.05); }
    .table thead th { background-color: #0b1220; color: #fff; }
    .table tbody tr:hover { background-color: rgba(255,255,255,0.03); }
    .btn-icon { display:inline-flex; align-items:center; gap:.4rem; }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">🧾 Lista de Compras</h3>
    <div>
      <a href="create.php" class="btn btn-primary btn-sm btn-icon"><i class="bi bi-plus-lg"></i> Nueva Compra</a>
      <a href="importar.php" class="btn btn-primary btn-sm btn-icon"><i class="bi bi-file-earmark-arrow-up"></i> Importar Excel</a>
    </div>
  </div>

  <div class="card p-3 shadow-sm">
    <div class="table-responsive">
      <table class="table table-borderless table-hover text-white align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Proveedor</th>
            <th>Email</th>
            <th>Producto</th>
            <th>Marca</th>
            <th>Fecha</th>
            <th class="text-end">Cantidad</th>
            <th class="text-end">V. Unitario</th>
            <th class="text-end">Total</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
<?php
$sql = "SELECT c.id, p.nombre AS proveedor, p.email, pr.nombre AS producto, pr.marca,
        c.fecha_compra, c.cantidad, pr.valor_unitario, c.valor_total
        FROM compras c
        JOIN proveedores p ON c.proveedor_id = p.id
        JOIN productos pr ON c.producto_id = pr.id
        ORDER BY c.id DESC";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()):
?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['proveedor']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['producto']) ?></td>
            <td><?= htmlspecialchars($row['marca']) ?></td>
            <td><?= $row['fecha_compra'] ?></td>
            <td class="text-end"><?= number_format($row['cantidad']) ?></td>
            <td class="text-end"><?= number_format($row['valor_unitario'],2) ?></td>
            <td class="text-end"><?= number_format($row['valor_total'],2) ?></td>
            <td>
              <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon"><i class="bi bi-pencil-square"></i> Editar</a>
              <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" onclick="return confirm('¿Eliminar compra?')"><i class="bi bi-trash"></i> Eliminar</a>
            </td>
          </tr>
<?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
