CREATE TABLE IF NOT EXISTS caja_ingresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caja_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 0,
    descripcion TEXT NULL,
    created DATETIME NULL,
    modified DATETIME NULL
);

CREATE TABLE IF NOT EXISTS caja_denominaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caja_id INT NOT NULL,
    tipo_movimiento VARCHAR(20) NULL,
    tipo VARCHAR(20) NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0,
    cantidad INT NOT NULL DEFAULT 0,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    created DATETIME NULL,
    modified DATETIME NULL
);

CREATE TABLE IF NOT EXISTS caja_egresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caja_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 0,
    descripcion TEXT NULL,
    created DATETIME NULL,
    modified DATETIME NULL
);

INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('Ingresos', 'index', 'Listar ingresos', NOW(), NOW()),
('Ingresos', 'view', 'Ver detalle de ingreso', NOW(), NOW()),
('Ingresos', 'add', 'Agregar ingreso', NOW(), NOW()),
('Egresos', 'index', 'Listar egresos', NOW(), NOW()),
('Egresos', 'view', 'Ver detalle de egreso', NOW(), NOW()),
('Egresos', 'add', 'Agregar egreso', NOW(), NOW());

