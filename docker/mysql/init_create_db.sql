-- SQL de inicialización para VidaPlus
-- Crea la base de datos y el usuario con privilegios

CREATE DATABASE IF NOT EXISTS `vidaplus` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'vidaplus'@'%' IDENTIFIED BY '3d1ba2b33c54cdb1f65b679b3a98f5c2adb7c9c0dbcb37c2';
GRANT ALL PRIVILEGES ON `vidaplus`.* TO 'vidaplus'@'%';
FLUSH PRIVILEGES;

-- Nota: Si el contenedor MySQL ya existe y usa datos persistentes, ejecutar este SQL
-- usando: docker-compose exec db mysql -u root -p < docker/mysql/init_create_db.sql
