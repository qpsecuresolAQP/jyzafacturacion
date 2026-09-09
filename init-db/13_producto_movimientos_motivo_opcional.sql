-- El motivo de un movimiento de stock (ingreso/egreso) debía ser opcional,
-- no obligatorio. Se permite NULL en la columna para representar "sin
-- motivo indicado" en vez de forzar un string vacío.
ALTER TABLE `producto_movimientos`
  MODIFY COLUMN `motivo` varchar(255) NULL DEFAULT NULL;
