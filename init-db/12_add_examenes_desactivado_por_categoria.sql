-- Igual que productos.desactivado_por_categoria: marca si un examen fue
-- desactivado automáticamente por la cascada al inactivar su categoría (vs.
-- desactivado manualmente por su cuenta). Al reactivar la categoría, solo
-- se reactivan los exámenes que la propia cascada apagó.
ALTER TABLE `examenes`
  ADD COLUMN `desactivado_por_categoria` tinyint(1) NOT NULL DEFAULT 0 AFTER `estado`;
