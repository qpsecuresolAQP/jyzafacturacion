CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    whatsapp VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created DATETIME NULL,
    modified DATETIME NULL
);

ALTER TABLE `productos`
  ADD COLUMN `proveedor_id` int(11) DEFAULT NULL AFTER `categoria_producto_id`,
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD CONSTRAINT `fk_productos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('Proveedores', 'index', 'Listar proveedores', NOW(), NOW()),
('Proveedores', 'view', 'Ver detalle de proveedor', NOW(), NOW()),
('Proveedores', 'add', 'Agregar proveedor', NOW(), NOW()),
('Proveedores', 'edit', 'Editar proveedor', NOW(), NOW()),
('Proveedores', 'delete', 'Eliminar proveedor', NOW(), NOW());
