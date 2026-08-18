# VidaPlus - Docker Setup

## Estructura de despliegue

```
/srv/containers/projects/spaziodentale/   # Compose + Dockerfiles
/srv/containers/projects/spaziodentale/init-db
/srv/containers/data/spaziodentale/db     # Datos persistentes de MySQL
```

## Arquitectura

- `app`: PHP 8.3 + Apache, construida con el `Dockerfile` del proyecto.
- `db`: MySQL 8.0 con volumen persistente y scripts en `init-db/`.
- `app_local.php`: se crea dentro del contenedor a partir de `config/app_local.example.php`.

## Variables de entorno

Editar [`.env`](.env) y ajustar:

- `DB_ROOT_PASSWORD`
- `DB_PASSWORD`
- `SECURITY_SALT`
- `PERUAPI_KEY`
- `APP_PORT`
- `DB_PORT`

## Arranque

```bash
cd /srv/containers/projects/spaziodentale
docker-compose up -d --build
```

## Verificación

```bash
docker-compose ps
docker-compose logs -f app
docker-compose logs -f db
```

## Base de datos

El contenedor `db` crea la base `spaziodentale` y el usuario con las variables de entorno. Los scripts en `init-db/` se ejecutan en orden alfabético al primer arranque con volumen vacío:

1. `01_factusqpestetico.sql` — asegura la base `spaziodentale` y el usuario `spaziodentale`.
2. `02_sistemad_factura.sql` — carga las tablas y los datos dentro de `spaziodentale` (el dump no contiene `CREATE DATABASE` ni `USE`, por lo que se importa contra la base definida en `MYSQL_DATABASE`).

> Para reimportar el dump tras cambiarlo, baja el stack y borra el volumen: `docker-compose down -v && docker-compose up -d`.

## CakePHP

El `entrypoint` crea `config/app_local.php` desde el ejemplo, por lo que la app toma:

- `DATABASE_URL`
- `SECURITY_SALT`
- `PERUAPI_KEY`
- `PERUAPI_BASE_URL`

## Comandos útiles

```bash
docker-compose exec app composer install
docker-compose exec app bin/cake migrations migrate
docker-compose exec db mysql -u spaziodentale -p spaziodentale
docker-compose down
docker-compose down -v
```
