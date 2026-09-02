-- Agrega el campo de gasto en materiales por tratamiento, para poder calcular
-- la utilidad real: costo cobrado al paciente - monto pagado al doctor - gasto en materiales.
ALTER TABLE `tratamientos`
  ADD COLUMN `gasto_materiales` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `monto_fijo_pago`;
