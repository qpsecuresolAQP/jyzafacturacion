ALTER TABLE presupuestos_tratamientos
  ADD COLUMN descuento_tipo VARCHAR(20) NOT NULL DEFAULT 'porcentaje' AFTER descuento;
