CREATE TABLE IF NOT EXISTS laboratorios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created DATETIME NULL,
    modified DATETIME NULL
);

CREATE TABLE IF NOT EXISTS invoice_distribuciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    tipo ENUM('LABORATORIO', 'MATERIALES') NOT NULL,
    laboratorio_id INT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 0,
    descripcion VARCHAR(255) NULL,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_invoice_distribuciones_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id),
    CONSTRAINT fk_invoice_distribuciones_laboratorio FOREIGN KEY (laboratorio_id) REFERENCES laboratorios (id)
);

INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('Laboratorios', 'index', 'Listar laboratorios', NOW(), NOW()),
('Laboratorios', 'view', 'Ver detalle de laboratorio', NOW(), NOW()),
('Laboratorios', 'add', 'Agregar laboratorio', NOW(), NOW()),
('Laboratorios', 'edit', 'Editar laboratorio', NOW(), NOW()),
('Laboratorios', 'delete', 'Eliminar laboratorio', NOW(), NOW());
