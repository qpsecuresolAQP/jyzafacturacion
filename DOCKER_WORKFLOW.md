# 🐳 Docker Workflow - Desarrollo vs Producción

## 📋 Problema Original

Antes: Cada cambio en un archivo requería reconstruir toda la imagen Docker.

Ahora: 
- **Desarrollo**: Código montado en volúmenes → sin reconstruir
- **Producción**: Solo reconstruye al hacer `git pull`

---

## 🎯 Flujo de Trabajo

### 👨‍💻 Desarrollo Local

```bash
# 1. Iniciar en modo desarrollo
./docker-manage.sh dev-start

# 2. Editar archivos (cambios instantáneos)
# Modificas: src/, templates/, config/, bin/
# ✅ Cambios reflejados sin reconstruir

# 3. Ver logs en tiempo real
./docker-manage.sh logs app

# 4. Conectar a shell del contenedor si necesitas
./docker-manage.sh shell

# 5. Ver estado
./docker-manage.sh status

# 6. Detener cuando termines
./docker-manage.sh dev-stop
```

### 🚀 Producción / Después de Pull

```bash
# Opción 1: Reconstruir manualmente
git pull
./docker-manage.sh prod-rebuild
./docker-manage.sh prod-start

# Opción 2: Todo automático (recomendado)
./docker-manage.sh prod-update
```

---

## 📁 Configuraciones

### `docker-compose.dev.yml` (Desarrollo)
- ✅ Sin reconstruir imagen
- ✅ Código montado como volúmenes
- ✅ DEBUG=true
- ✅ Hot-reload de cambios

**Archivos montados:**
```
./src          → /var/www/html/src
./templates    → /var/www/html/templates
./config       → /var/www/html/config
./bin          → /var/www/html/bin
```

### `docker-compose.yml` (Producción)
- ✅ Sin referencia a `build: .`
- ✅ Usa imagen preexistente
- ✅ Volumes solo para datos persistentes
- ✅ DEBUG=false

**Volúmenes solo para datos:**
```
./webroot/img
./webroot/uploads
./webroot/xml
./webroot/cdr
./webroot/pdf
./webroot/certificados
app_logs
app_tmp
```

---

## 🔧 Dockerfile

No cambió, pero ahora se usa así:

**Primera vez / Después de cambios en dependencias:**
```bash
# Build manual
docker-compose -f docker-compose.yml build app

# O con el script
./docker-manage.sh prod-rebuild
```

**Cambios en código PHP/templates:**
```bash
# NO necesitas reconstruir en desarrollo
./docker-manage.sh dev-start
# Edita archivos
# ✅ Cambios reflejados inmediatamente
```

---

## 📊 Comparativa

| Aspecto | Antes | Ahora |
|---------|-------|-------|
| **Cambio en PHP** | Reconstruir imagen | Sin reconstruir (dev) |
| **Cambio en templates** | Reconstruir imagen | Sin reconstruir (dev) |
| **Cambio en config** | Reconstruir imagen | Sin reconstruir (dev) |
| **Tiempo por cambio** | ~2-5 min | ~0 segundos |
| **Pull en prod** | Reconstruir manual | `prod-update` |
| **Frecuencia de rebuild** | Cada cambio | Solo cuando sea necesario |

---

## 🚦 Cuando Reconstruir

### ❌ NO necesitas reconstruir en desarrollo para:
- Cambios en `src/`
- Cambios en `templates/`
- Cambios en `config/`
- Cambios en `bin/`

### ✅ SÍ necesitas reconstruir cuando cambies:
- `Dockerfile`
- `composer.json` (nuevas dependencias)
- `docker/php.ini`
- `docker/apache.conf`
- `docker/entrypoint.sh`

---

## 🎮 Comandos Disponibles

```bash
./docker-manage.sh dev-start      # Inicia en desarrollo
./docker-manage.sh dev-stop       # Detiene desarrollo

./docker-manage.sh prod-start     # Inicia en producción
./docker-manage.sh prod-rebuild   # Reconstruye imagen
./docker-manage.sh prod-update    # Pull + rebuild + start

./docker-manage.sh logs [app|db]  # Ver logs
./docker-manage.sh shell          # Shell interactivo
./docker-manage.sh status         # Estado de contenedores
./docker-manage.sh restart        # Reiniciar contenedores
./docker-manage.sh clean          # Detener y limpiar
./docker-manage.sh --help         # Ver ayuda
```

---

## 📝 Ejemplos de Flujo Completo

### Ejemplo 1: Desarrollo Diario

```bash
# Al iniciar
cd /fjqp/jyzafacturacion
./docker-manage.sh dev-start

# Ver que todo está bien
./docker-manage.sh status

# Editar src/Controller/FacturasController.php
nano src/Controller/FacturasController.php

# Guardar y refrescar navegador
# ✅ Cambios visibles inmediatamente (sin reconstruir)

# Ver logs si hay errores
./docker-manage.sh logs app

# Si necesitas ejecutar comandos
./docker-manage.sh shell
$ bin/cake migrations migrate
$ exit

# Al terminar
./docker-manage.sh dev-stop
```

### Ejemplo 2: Actualización de Repositorio

```bash
# Opción automática (recomendada)
./docker-manage.sh prod-update
# Hace: git pull + reconstruir + iniciar

# Opción manual si lo prefieres
git pull
./docker-manage.sh prod-rebuild
./docker-manage.sh prod-start

# Verificar
./docker-manage.sh status
./docker-manage.sh logs app
```

### Ejemplo 3: Cambio en composer.json

```bash
# Necesita reconstruir porque cambió una dependencia
nano composer.json  # Agregar librería

# Actualizar y reconstruir
git add composer.json
git commit -m "Agregar nueva librería"
git push

# En producción
./docker-manage.sh prod-update
# Reconstruye y aplica nuevas dependencias

# En desarrollo, si usas dev-start
./docker-manage.sh dev-stop
./docker-manage.sh prod-rebuild
./docker-manage.sh dev-start
# Los volúmenes montados usarán la nueva imagen
```

---

## 🔍 Verificación

```bash
# Ver qué archivo está usando
cat docker-compose.yml | head -5

# Ver si está en desarrollo
docker ps | grep jyzafacturacion_app

# Ver volúmenes montados
docker inspect jyzafacturacion_app | grep -A 20 Mounts

# Verificar que los cambios se ven
curl http://localhost:8092/  # o tu puerto configurado
```

---

## ⚠️ Notas Importantes

1. **Desarrollo**: Los datos en volúmenes persisten entre reinicios
2. **Producción**: La imagen incluye todo el código (no depende de volúmenes de código)
3. **Git pull**: Siempre hace `git pull` antes de reconstruir en `prod-update`
4. **Caché Docker**: Los rebuilds usan caché, segundo rebuild es mucho más rápido
5. **Logs**: Montados en volúmenes, persisten aunque derribes los contenedores

---

## 🆘 Troubleshooting

### "Los cambios no se ven"
```bash
# Verificar que estás en modo desarrollo
docker-compose ps  # Debe mostrar volúmenes montados

# Refresh del navegador
# Limpia caché del navegador (Ctrl+Shift+Delete)
```

### "Necesito reconstruir"
```bash
# Si estás en dev pero necesitas cambios de Dockerfile
./docker-manage.sh dev-stop
./docker-manage.sh prod-rebuild  # Reconstruye imagen
./docker-manage.sh dev-start     # Inicia con nueva imagen
```

### "Error de permisos en volúmenes"
```bash
# El contenedor usa www-data
# Los archivos deben ser legibles por el contenedor
chmod 755 src/
chmod 755 templates/
chmod 755 config/
chmod 755 bin/
```

---

## 📚 Referencias

- **Dockerfile**: `Dockerfile` (no cambió)
- **Compose producción**: `docker-compose.yml`
- **Compose desarrollo**: `docker-compose.dev.yml`
- **Script gestor**: `docker-manage.sh`
- **Documentación original**: `DOCKER_SETUP.md`

