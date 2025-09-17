-- database.sql for MiElementos
CREATE DATABASE IF NOT EXISTS mielementos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mielementos;

CREATE TABLE proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(80) NOT NULL,
  nombre VARCHAR(200) NOT NULL,
  email VARCHAR(150),
  UNIQUE KEY ux_proveedores_documento (documento)
);

CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(120) NOT NULL,
  nombre VARCHAR(250) NOT NULL,
  marca VARCHAR(150),
  valor_unitario DECIMAL(18,2) NOT NULL DEFAULT 0,
  UNIQUE KEY ux_productos_codigo (codigo)
);

CREATE TABLE compras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  proveedor_id INT NOT NULL,
  fecha_compra DATETIME NOT NULL,
  valor_total DECIMAL(18,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_fecha_compra (fecha_compra)
);

CREATE TABLE compras_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  compra_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad DECIMAL(12,2) NOT NULL,
  valor_unitario DECIMAL(18,2) NOT NULL,
  valor_total DECIMAL(18,2) NOT NULL,
  FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_compra_id (compra_id)
);
