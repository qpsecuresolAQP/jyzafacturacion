#!/bin/bash

# Script para gestionar Docker Compose con diferentes configuraciones
# Uso: ./docker-manage.sh [comando] [opciones]

set -e

COMPOSE_FILE_DEV="docker-compose.dev.yml"
COMPOSE_FILE_PROD="docker-compose.yml"
COMPOSE_FILE_BASE="docker-compose.yml"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

show_help() {
    cat << EOF
${BLUE}=== Docker Compose Manager ===${NC}

${GREEN}Uso:${NC} ./docker-manage.sh [comando] [opciones]

${GREEN}Comandos:${NC}

  ${YELLOW}dev-start${NC}
    Inicia en modo desarrollo (sin reconstruir imagen)
    Monta código fuente para hot-reload

  ${YELLOW}dev-stop${NC}
    Detiene los contenedores en modo desarrollo

  ${YELLOW}prod-start${NC}
    Inicia en modo producción (usa imagen existente)

  ${YELLOW}prod-rebuild${NC}
    Reconstruye la imagen de producción
    Debe ejecutarse después de un pull

  ${YELLOW}prod-update${NC}
    Equivalente a: git pull + prod-rebuild + prod-start

  ${YELLOW}logs${NC}
    Muestra logs en tiempo real
    Usar: ./docker-manage.sh logs [app|db]

  ${YELLOW}shell${NC}
    Abre shell interactivo en el contenedor app

  ${YELLOW}status${NC}
    Muestra estado actual de los contenedores

  ${YELLOW}restart${NC}
    Reinicia los contenedores

  ${YELLOW}clean${NC}
    Detiene y elimina contenedores (sin perder datos)

${GREEN}Ejemplos:${NC}

  # Desarrollo
  ./docker-manage.sh dev-start
  ./docker-manage.sh logs app
  ./docker-manage.sh shell

  # Producción
  ./docker-manage.sh prod-start
  ./docker-manage.sh prod-rebuild
  ./docker-manage.sh prod-update

EOF
}

dev_start() {
    echo -e "${BLUE}🚀 Iniciando en modo DESARROLLO...${NC}"
    echo -e "${YELLOW}ℹ️  El código fuente está montado para hot-reload${NC}"
    docker-compose -f "$COMPOSE_FILE_DEV" up -d
    echo -e "${GREEN}✅ Contenedores iniciados en modo desarrollo${NC}"
    echo -e "${BLUE}📝 Los cambios en código se reflejan inmediatamente${NC}"
}

dev_stop() {
    echo -e "${BLUE}⏹️  Deteniendo modo desarrollo...${NC}"
    docker-compose -f "$COMPOSE_FILE_DEV" down
    echo -e "${GREEN}✅ Contenedores detenidos${NC}"
}

prod_start() {
    echo -e "${BLUE}🚀 Iniciando en modo PRODUCCIÓN...${NC}"
    docker-compose -f "$COMPOSE_FILE_PROD" up -d
    echo -e "${GREEN}✅ Contenedores iniciados en modo producción${NC}"
}

prod_rebuild() {
    echo -e "${BLUE}🔨 Reconstruyendo imagen de producción...${NC}"
    docker-compose -f "$COMPOSE_FILE_PROD" build --no-cache app
    echo -e "${GREEN}✅ Imagen reconstruida${NC}"
    echo -e "${YELLOW}ℹ️  Ejecuta 'prod-start' para iniciar con la nueva imagen${NC}"
}

prod_update() {
    echo -e "${BLUE}📥 Actualizando desde repositorio...${NC}"
    git pull
    echo -e "${BLUE}🔨 Reconstruyendo imagen...${NC}"
    prod_rebuild
    echo -e "${BLUE}🚀 Iniciando contenedores...${NC}"
    prod_start
    echo -e "${GREEN}✅ Actualización completada${NC}"
}

show_logs() {
    local service="${1:-app}"
    echo -e "${BLUE}📋 Mostrando logs de $service...${NC}"
    docker-compose logs -f "$service"
}

open_shell() {
    echo -e "${BLUE}🐚 Abriendo shell en contenedor...${NC}"
    docker-compose exec app bash
}

show_status() {
    echo -e "${BLUE}📊 Estado de contenedores:${NC}"
    docker-compose ps
}

restart_containers() {
    echo -e "${BLUE}🔄 Reiniciando contenedores...${NC}"
    docker-compose restart
    echo -e "${GREEN}✅ Contenedores reiniciados${NC}"
}

clean_containers() {
    echo -e "${YELLOW}⚠️  Esto detendrá y eliminará los contenedores (sin perder datos)${NC}"
    read -p "¿Estás seguro? (s/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        echo -e "${BLUE}🗑️  Limpiando contenedores...${NC}"
        docker-compose down
        echo -e "${GREEN}✅ Contenedores eliminados${NC}"
    else
        echo -e "${YELLOW}Cancelado${NC}"
    fi
}

# Main
if [ $# -eq 0 ]; then
    show_help
    exit 0
fi

case "$1" in
    dev-start)
        dev_start
        ;;
    dev-stop)
        dev_stop
        ;;
    prod-start)
        prod_start
        ;;
    prod-rebuild)
        prod_rebuild
        ;;
    prod-update)
        prod_update
        ;;
    logs)
        show_logs "$2"
        ;;
    shell)
        open_shell
        ;;
    status)
        show_status
        ;;
    restart)
        restart_containers
        ;;
    clean)
        clean_containers
        ;;
    -h|--help|help)
        show_help
        ;;
    *)
        echo -e "${RED}❌ Comando desconocido: $1${NC}"
        echo -e "${YELLOW}Usa: ./docker-manage.sh --help${NC}"
        exit 1
        ;;
esac
