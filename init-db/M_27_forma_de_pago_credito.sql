ALTER TABLE invoices
    ADD COLUMN forma_pago VARCHAR(10) NOT NULL DEFAULT 'CONTADO' AFTER estado;

CREATE TABLE invoice_cuotas (
    id INT(11) NOT NULL AUTO_INCREMENT,
    invoice_id INT(11) NOT NULL,
    numero_cuota TINYINT UNSIGNED NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE',
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_invoice_id (invoice_id),
    CONSTRAINT fk_invoice_cuotas_invoice
        FOREIGN KEY (invoice_id)
        REFERENCES invoices(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE invoice_cuotas
    ADD COLUMN metodo_pago VARCHAR(20) NULL AFTER estado,
    ADD COLUMN caja_id INT NULL AFTER metodo_pago,
    ADD COLUMN pagado_en DATETIME NULL AFTER caja_id;