<?php
// importar_csv.php - importador CSV para MiElementos (lee filas y crea compras)
require 'conexion.php';

if (!isset($_FILES['csvfile'])) {
  die("No se subió archivo.");
}
if ($_FILES['csvfile']['error'] !== UPLOAD_ERR_OK) {
  die("Error al subir.");
}

$tmp = $_FILES['csvfile']['tmp_name'];
if (($handle = fopen($tmp, 'r')) === false) {
  die("No se pudo abrir archivo.");
}

$pdo->beginTransaction();
try {
  $row = 0;
  while (($data = fgetcsv($handle, 0, ',')) !== false) {
    $row++;
    if (count($data) < 9) continue;
    list($prov_doc,$prov_nombre,$prov_email,$fecha_compra,$codigo,$nombre_producto,$marca,$cantidad,$valor_unitario) = array_map('trim', $data);
    if (!$prov_doc || !$codigo) continue;

    // Upsert proveedor
    $stmt = $pdo->prepare("SELECT id FROM proveedores WHERE documento = ?");
    $stmt->execute([$prov_doc]);
    $prov_id = $stmt->fetchColumn();
    if (!$prov_id) {
      $stmt = $pdo->prepare("INSERT INTO proveedores (documento, nombre, email) VALUES (?, ?, ?)");
      $stmt->execute([$prov_doc, $prov_nombre, $prov_email]);
      $prov_id = $pdo->lastInsertId();
    }

    // Insert compra
    $stmt = $pdo->prepare("INSERT INTO compras (proveedor_id, fecha_compra, valor_total) VALUES (?, ?, ?)");
    $stmt->execute([$prov_id, $fecha_compra, 0]);
    $compra_id = $pdo->lastInsertId();

    // Upsert producto
    $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $prod_id = $stmt->fetchColumn();
    if (!$prod_id) {
      $stmt = $pdo->prepare("INSERT INTO productos (codigo, nombre, marca, valor_unitario) VALUES (?, ?, ?, ?)");
      $stmt->execute([$codigo, $nombre_producto, $marca, floatval($valor_unitario)]);
      $prod_id = $pdo->lastInsertId();
    }

    $vt = round(floatval($cantidad) * floatval($valor_unitario), 2);
    $stmt = $pdo->prepare("INSERT INTO compras_detalle (compra_id, producto_id, cantidad, valor_unitario, valor_total) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$compra_id, $prod_id, floatval($cantidad), floatval($valor_unitario), $vt]);

    $stmt = $pdo->prepare("UPDATE compras SET valor_total = ? WHERE id = ?");
    $stmt->execute([$vt, $compra_id]);
  }

  $pdo->commit();
  fclose($handle);
  header('Location: index.php?msg=import_ok');
  exit;

} catch (Exception $e) {
  $pdo->rollBack();
  fclose($handle);
  die('Error importando CSV: ' . $e->getMessage());
}
?>