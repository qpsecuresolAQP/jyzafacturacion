-- Asigna serie RI y correlativo basado en el ID
-- para recibos internos que aún no tienen serie.

-- Asignar serie RI a todos los recibos internos que aún no tienen serie
UPDATE invoices 
SET serie = 'RI', correlativo = id
WHERE estado = 'RECIBO_INTERNO' 
  AND (serie IS NULL OR serie = '');