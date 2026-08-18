CREATE TABLE IF NOT EXISTS categorias_examenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    created DATETIME NULL,
    modified DATETIME NULL
);

CREATE TABLE IF NOT EXISTS examenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_examen_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    muestra VARCHAR(150) NULL,
    precio_convenio DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_examenes_categoria FOREIGN KEY (categoria_examen_id) REFERENCES categorias_examenes (id)
);

ALTER TABLE invoice_items
  ADD COLUMN examen_id INT NULL AFTER producto_id,
  ADD CONSTRAINT fk_invoice_items_examen FOREIGN KEY (examen_id) REFERENCES examenes (id);

INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('Examenes', 'index', 'Listar exámenes', NOW(), NOW()),
('Examenes', 'view', 'Ver detalle de examen', NOW(), NOW()),
('Examenes', 'add', 'Agregar examen', NOW(), NOW()),
('Examenes', 'edit', 'Editar examen', NOW(), NOW()),
('Examenes', 'delete', 'Eliminar examen', NOW(), NOW()),
('CategoriasExamenes', 'index', 'Listar categorías de exámenes', NOW(), NOW()),
('CategoriasExamenes', 'view', 'Ver detalle de categoría de examen', NOW(), NOW()),
('CategoriasExamenes', 'add', 'Agregar categoría de examen', NOW(), NOW()),
('CategoriasExamenes', 'edit', 'Editar categoría de examen', NOW(), NOW()),
('CategoriasExamenes', 'delete', 'Eliminar categoría de examen', NOW(), NOW());
