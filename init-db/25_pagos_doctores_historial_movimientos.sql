CREATE TABLE IF NOT EXISTS pagos_doctores_historial_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pago_historial_id INT NOT NULL,
    caja_movimiento_id INT NOT NULL,
    invoice_id INT NOT NULL,
    metodo_pago VARCHAR(20) NOT NULL,
    base_doctor DECIMAL(10,2) NOT NULL DEFAULT 0,
    monto_pagado DECIMAL(10,2) NOT NULL DEFAULT 0,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_pdhm_pago_historial FOREIGN KEY (pago_historial_id) REFERENCES pagos_doctores_historial (id),
    CONSTRAINT fk_pdhm_caja_movimiento FOREIGN KEY (caja_movimiento_id) REFERENCES caja_movimientos (id),
    CONSTRAINT fk_pdhm_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id),
    UNIQUE KEY uq_pdhm_caja_movimiento (caja_movimiento_id)
);
