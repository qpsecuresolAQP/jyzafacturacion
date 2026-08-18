CREATE TABLE IF NOT EXISTS presupuestos_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    presupuesto_id INT NOT NULL,
    invoice_id INT NOT NULL,
    monto_facturado DECIMAL(10,2) NOT NULL DEFAULT 0,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_presupuestos_invoices_presupuesto FOREIGN KEY (presupuesto_id) REFERENCES presupuestos (id),
    CONSTRAINT fk_presupuestos_invoices_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id)
);
