ALTER TABLE doctores
  ADD COLUMN modo_pago ENUM('PORCENTAJE', 'FIJO') NOT NULL DEFAULT 'PORCENTAJE' AFTER porcentaje_pago;

CREATE TABLE IF NOT EXISTS doctor_tratamiento_tarifas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    tratamiento_id INT NOT NULL,
    monto_fijo DECIMAL(10,2) NOT NULL DEFAULT 0,
    created DATETIME NULL,
    modified DATETIME NULL,
    CONSTRAINT fk_dtt_doctor FOREIGN KEY (doctor_id) REFERENCES doctores (id),
    CONSTRAINT fk_dtt_tratamiento FOREIGN KEY (tratamiento_id) REFERENCES tratamientos (id),
    UNIQUE KEY uq_dtt_doctor_tratamiento (doctor_id, tratamiento_id)
);

-- Las tarifas fijas se gestionan dentro de DoctoresController::edit(),
-- reutilizando los permisos existentes de 'Doctores' (no requiere permisos propios).
