ALTER TABLE presupuestos_tratamientos
  MODIFY COLUMN tratamiento_id INT(11) NULL,
  ADD COLUMN tipo_item VARCHAR(20) NOT NULL DEFAULT 'tratamiento' AFTER tratamiento_id,
  ADD COLUMN producto_id INT(11) NULL AFTER tipo_item,
  ADD COLUMN examen_id INT(11) NULL AFTER producto_id;

ALTER TABLE presupuestos_tratamientos
  ADD CONSTRAINT fk_presupuestos_tratamientos_producto FOREIGN KEY (producto_id) REFERENCES productos (id),
  ADD CONSTRAINT fk_presupuestos_tratamientos_examen FOREIGN KEY (examen_id) REFERENCES examenes (id);
