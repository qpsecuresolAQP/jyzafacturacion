INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('PagosDoctores', 'index', 'Ver reporte de pagos a doctores', NOW(), NOW()),
('PagosDoctores', 'exportPdf', 'Exportar reporte de pagos a doctores (PDF)', NOW(), NOW()),
('PagosDoctores', 'exportarExcel', 'Exportar reporte de pagos a doctores (Excel)', NOW(), NOW()),
('PagosDoctores', 'marcarPagado', 'Marcar pago a doctor como realizado', NOW(), NOW()),
('PagosDoctores', 'historial', 'Ver historial de pagos a doctores', NOW(), NOW()),
('PagosDoctores', 'historialDetalle', 'Ver detalle de un pago a doctor', NOW(), NOW());
