-- Los ingresos externos de caja pedían un método de pago en el formulario,
-- pero la tabla no tenía dónde guardarlo: todo se contaba como efectivo.
-- Los registros existentes quedan como EFECTIVO (así se contaban hasta ahora).
ALTER TABLE `caja_ingresos`
  ADD COLUMN `metodo_pago` varchar(50) NOT NULL DEFAULT 'EFECTIVO' AFTER `monto`;
