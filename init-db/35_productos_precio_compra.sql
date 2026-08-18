ALTER TABLE `productos`
  ADD COLUMN `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `precio`;
