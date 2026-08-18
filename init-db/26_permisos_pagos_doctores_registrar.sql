DELETE FROM permisos WHERE controller = 'PagosDoctores' AND action = 'marcarPagado';

INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('PagosDoctores', 'registrarPago', 'Ver pantalla de registro de pago a doctor', NOW(), NOW()),
('PagosDoctores', 'guardarPago', 'Guardar pago a doctor', NOW(), NOW());
