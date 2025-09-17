<?php
// insertar_compra.php - procesa compra recibida por AJAX y responde JSON
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

try {
  $prov_doc = trim($_POST['prov_documento'] ?? '');
  $prov_nombre = trim($_POST['prov_nombre'] ?? '');
  $prov_email = trim($_POST['prov_email'] ?? '');
  $fecha_compra = $_POST['fecha_compra'] ?? '';

  if (!$prov_doc || !$prov_nombre || !$fecha_compra) {
    echo json_encode(['success'=>false,'message'=>'Faltan campos obligatorios.']); exit;
  }

  if (strpos($fecha_compra,'T') !== false) {
    $fecha_compra = str_replace('T',' ',$fecha_compra) . ':00';
  }

  $codigos = $_POST['codigo'] ?? [];
  $nombres = $_POST['nombre_producto'] ?? [];
  $marcas = $_POST['marca'] ?? [];
  $cantidades = $_POST['cantidad'] ?? [];
  $valores = $_POST['valor_unitario'] ?? [];

  if (count($codigos) === 0) {
    echo json_encode(['success'=>false,'message'=>'No hay productos.']); exit;
  }

  $pdo->beginTransaction();

  // Upsert proveedor por documento
  $stmt = $pdo->prepare("SELECT id FROM proveedores WHERE documento = ?");
  $stmt->execute([$prov_doc]);
  $prov_id = $stmt->fetchColumn();
  if (!$prov_id) {
    $stmt = $pdo->prepare("INSERT INTO proveedores (documento, nombre, email) VALUES (?, ?, ?)");
    $stmt->execute([$prov_doc, $prov_nombre, $prov_email]);
    $prov_id = $pdo->lastInsertId();
  } else {
    $stmt = $pdo->prepare("UPDATE proveedores SET nombre = ?, email = ? WHERE id = ?");
    $stmt->execute([$prov_nombre, $prov_email, $prov_id]);
  }

  // Insert cabecera compra (temporal valor_total 0)
  $stmt = $pdo->prepare("INSERT INTO compras (proveedor_id, fecha_compra, valor_total) VALUES (?, ?, ?)");
  $stmt->execute([$prov_id, $fecha_compra, 0]);
  $compra_id = $pdo->lastInsertId();

  $total_compra = 0;
  for ($i=0; $i<count($codigos); $i++) {
    $codigo = trim($codigos[$i]);
    $nombre = trim($nombres[$i]);
    $marca = trim($marcas[$i] ?? '');
    $cantidad = floatval($cantidades[$i]);
    $vu = floatval($valores[$i]);
    $vt = round($cantidad * $vu, 2);
    $total_compra += $vt;

    // Upsert producto por codigo
    $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $prod_id = $stmt->fetchColumn();
    if (!$prod_id) {
      $stmt = $pdo->prepare("INSERT INTO productos (codigo, nombre, marca, valor_unitario) VALUES (?, ?, ?, ?)");
      $stmt->execute([$codigo, $nombre, $marca, $vu]);
      $prod_id = $pdo->lastInsertId();
    } else {
      $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, marca = ?, valor_unitario = ? WHERE id = ?");
      $stmt->execute([$nombre, $marca, $vu, $prod_id]);
    }

    // Insert detalle
    $stmt = $pdo->prepare("INSERT INTO compras_detalle (compra_id, producto_id, cantidad, valor_unitario, valor_total) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$compra_id, $prod_id, $cantidad, $vu, $vt]);
  }

  // Actualizar total compra
  $stmt = $pdo->prepare("UPDATE compras SET valor_total = ? WHERE id = ?");
  $stmt->execute([$total_compra, $compra_id]);

  $pdo->commit();
  echo json_encode(['success'=>true, 'compra_id'=>$compra_id]);

} catch (Exception $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
?>