-- Todos los productos usan código interno generado a partir del id (PROD-001, PROD-002, ...).
-- Reemplaza los códigos ingresados a mano. Desde ahora ProductosTable::afterSave lo asigna al guardar.
UPDATE `productos`
SET `codigo` = CONCAT('PROD-', LPAD(`id`, 3, '0'));
