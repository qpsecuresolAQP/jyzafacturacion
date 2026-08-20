-- SQL de inicialización para jyzafacturacion
-- Crea la base de datos y el usuario con privilegios

CREATE DATABASE IF NOT EXISTS `jyzafacturacion` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'jyzafacturacion'@'%' IDENTIFIED BY '3d1ba2b33c54cdb1f65b679b3a98f5c2adb7c9c0dbcb37c2';
GRANT ALL PRIVILEGES ON `jyzafacturacion`.* TO 'jyzafacturacion'@'%';
FLUSH PRIVILEGES;

-- Nota: Si el contenedor MySQL ya existe y usa datos persistentes, ejecutar este SQL
-- usando: docker-compose exec db mysql -u root -p < docker/mysql/init_create_db.sql
