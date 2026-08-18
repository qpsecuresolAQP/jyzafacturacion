--
-- Campos para descuentos y nombre SUNAT en invoice_items
--

ALTER TABLE invoice_items 
    ADD COLUMN descuento DECIMAL(10,4) NOT NULL DEFAULT 0 AFTER precio_unitario,
    ADD COLUMN descuento_tipo VARCHAR(20) NOT NULL DEFAULT 'porcentaje' AFTER descuento;

ALTER TABLE invoice_items 
    ADD COLUMN nombre_sunat VARCHAR(250) NULL AFTER descripcion;