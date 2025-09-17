MiElementos - Módulo de Compras (Entrega)
-----------------------------------------

Contenido del paquete:
- index.php               -> Formulario principal (HTML + Bootstrap + AJAX)
- script.js               -> Lógica JavaScript (manejo de filas, fetch AJAX)
- style.css               -> Estilos
- conexion.php            -> Conexión PDO (configurar credenciales)
- insertar_compra.php     -> Endpoint AJAX para guardar compra
- importar_csv.php        -> Importador CSV para registros legacy (~1300 filas)
- reportes.php            -> Reporte visual con filtros y Bootstrap
- export_csv.php          -> Exporta reporte a CSV
- database.sql            -> Script para crear BD y tablas
- diagram.txt             -> MER en texto
- README.txt              -> Este archivo

Instrucciones:
1) Copia la carpeta al servidor (htdocs o www).
2) Ajusta conexion.php si tu DB usa otras credenciales.
3) Ejecuta database.sql en phpMyAdmin o consola.
4) Accede a index.php y prueba registrar compras y la importación CSV.

CSV de ejemplo (una fila por compra + producto):
prov_documento,prov_nombre,prov_email,fecha_compra,codigo,nombre_producto,marca,cantidad,valor_unitario
12345678,Proveedor A,provea@mail.com,2025-09-01 10:00:00,PR-001,Pulidora,MarcaX,2,120000.00
