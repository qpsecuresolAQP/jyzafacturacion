ALTER TABLE tratamientos
  ADD COLUMN estado TINYINT(1) NOT NULL DEFAULT 1 AFTER monto_fijo_pago;
