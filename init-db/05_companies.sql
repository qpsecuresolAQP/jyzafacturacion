SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE companies;
SET FOREIGN_KEY_CHECKS = 1;
INSERT INTO companies (
    ruc,
    razon_social,
    nombre_comercial,
    direccion,
    ubigeo,
    departamento,
    provincia,
    distrito,
    urbanizacion,
    cod_local,
    sol_user,
    sol_pass,
    certificado_sunat,
    ambiente,
    created,
    modified
) VALUES (
    '20607602051',
    'LAB DENTAL TECHNOLOGY YUWER E.I.R.L.',
    'LAB DENTAL TECHNOLOGY YUWER',
    'CAL. VALENTIN ESPEJO NRO. 602 INT. A URB. SAN JUAN ZN. E-DOS B',
    '150133',
    'LIMA',
    'LIMA',
    'SAN JUAN DE MIRAFLORES',
    'SAN JUAN',
    '0000',
    'usersecundario',
    'usersecundariopassword',
    'certificados/certificadoyuwer.pem',
    'beta',
    NOW(),
    NOW()
);