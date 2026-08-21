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
    '20609186039',
    'CAYCHO CABRERA & ASOCIADOS S.A.C.',
    'CONSULTORIO GINECOLÓGICO JYZA',
    'JR. 2 DE MAYO NRO. 1600 HUANUCO - HUANUCO - HUANUCO',
    '100101',
    'HUANUCO',
    'HUANUCO',
    'HUANUCO',
    'null',
    '0000',
    'usersecundario',
    'usersecundariopassword',
    'certificados/LLAMAPECERTIFICADODEMO20203040_cert_out.pem',
    'beta',
    NOW(),
    NOW()
);