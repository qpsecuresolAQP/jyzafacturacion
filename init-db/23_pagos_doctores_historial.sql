CREATE TABLE IF NOT EXISTS pagos_doctores_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    user_id INT NULL,
    fecha_desde DATE NOT NULL,
    fecha_hasta DATE NOT NULL,
    monto_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_comprobantes INT NOT NULL DEFAULT 0,
    observaciones TEXT NULL,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_pagos_doctores_historial_doctor FOREIGN KEY (doctor_id) REFERENCES doctores (id),
    CONSTRAINT fk_pagos_doctores_historial_user FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS pagos_doctores_historial_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pago_historial_id INT NOT NULL,
    invoice_id INT NOT NULL,
    base_doctor DECIMAL(10,2) NOT NULL DEFAULT 0,
    monto_pagado DECIMAL(10,2) NOT NULL DEFAULT 0,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_pdhi_pago_historial FOREIGN KEY (pago_historial_id) REFERENCES pagos_doctores_historial (id),
    CONSTRAINT fk_pdhi_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id),
    UNIQUE KEY uq_pdhi_invoice (invoice_id)
);
