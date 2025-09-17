<?php
include 'db.php';
$id = intval($_GET['id'] ?? 0);
if($id){
    $conn->query("DELETE FROM compras WHERE id=$id");
}
header('Location: index.php');
exit;
?>