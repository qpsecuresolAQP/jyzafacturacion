CREATE TABLE IF NOT EXISTS pagos_laboratorios_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    laboratorio_id INT NOT NULL,
    user_id INT NULL,
    fecha_desde DATE NOT NULL,
    fecha_hasta DATE NOT NULL,
    monto_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_comprobantes INT NOT NULL DEFAULT 0,
    observaciones TEXT NULL,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_plh_laboratorio FOREIGN KEY (laboratorio_id) REFERENCES laboratorios (id),
    CONSTRAINT fk_plh_user FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS pagos_laboratorios_historial_distribuciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pago_historial_id INT NOT NULL,
    invoice_distribucion_id INT NOT NULL,
    invoice_id INT NOT NULL,
    monto_pagado DECIMAL(10,2) NOT NULL DEFAULT 0,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_plhd_pago_historial FOREIGN KEY (pago_historial_id) REFERENCES pagos_laboratorios_historial (id),
    CONSTRAINT fk_plhd_invoice_distribucion FOREIGN KEY (invoice_distribucion_id) REFERENCES invoice_distribuciones (id),
    CONSTRAINT fk_plhd_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id),
    UNIQUE KEY uq_plhd_invoice_distribucion (invoice_distribucion_id)
);

INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('PagosLaboratorios', 'index', 'Ver reporte de pagos a laboratorios', NOW(), NOW()),
('PagosLaboratorios', 'registrarPago', 'Ver pantalla de registro de pago a laboratorio', NOW(), NOW()),
('PagosLaboratorios', 'guardarPago', 'Guardar pago a laboratorio', NOW(), NOW()),
('PagosLaboratorios', 'historial', 'Ver historial de pagos a laboratorios', NOW(), NOW()),
('PagosLaboratorios', 'historialDetalle', 'Ver detalle de un pago a laboratorio', NOW(), NOW());
