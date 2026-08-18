ALTER TABLE tratamientos
  ADD COLUMN monto_fijo_pago DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER costo;
