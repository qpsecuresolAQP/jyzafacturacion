-- Marca si un producto fue desactivado automáticamente por la cascada al
-- inactivar su categoría (vs. desactivado manualmente por el usuario). Así,
-- al reactivar la categoría, solo se reactivan los productos que la propia
-- cascada apagó, sin tocar los que ya estaban inactivos por su cuenta.
ALTER TABLE `productos`
  ADD COLUMN `desactivado_por_categoria` tinyint(1) NOT NULL DEFAULT 0 AFTER `estado`;
