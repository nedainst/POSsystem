#!/bin/bash

# ==========================================
#   NedaPOS - Start Server
# ==========================================

cd "$(dirname "$0")"

PORT=8000
HOST="0.0.0.0"

# Warna
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
NC='\033[0m'
BOLD='\033[1m'

echo ""
echo -e "${CYAN}╔══════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║        ${BOLD}🛒  NedaPOS - Sistem Kasir${NC}${CYAN}        ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════╝${NC}"
echo ""

# Cek PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}✗ PHP tidak ditemukan. Silakan install PHP terlebih dahulu.${NC}"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo -e "${GREEN}✓${NC} PHP ${PHP_VERSION} ditemukan"

# Cek Composer dependencies
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}⟳ Menginstall dependencies...${NC}"
    composer install --no-interaction --quiet
    echo -e "${GREEN}✓${NC} Dependencies terinstall"
else
    echo -e "${GREEN}✓${NC} Dependencies sudah tersedia"
fi

# Cek file .env
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⟳ Membuat file .env...${NC}"
    cp .env.example .env
    php artisan key:generate --quiet
    echo -e "${GREEN}✓${NC} File .env dibuat"
else
    echo -e "${GREEN}✓${NC} File .env tersedia"
fi

# Cek database
if [ ! -f "database/database.sqlite" ]; then
    echo -e "${YELLOW}⟳ Membuat database...${NC}"
    touch database/database.sqlite
    php artisan migrate --seed --force --quiet
    echo -e "${GREEN}✓${NC} Database dibuat dan diisi data awal"
else
    echo -e "${GREEN}✓${NC} Database tersedia"
fi

# Cek storage link
if [ ! -L "public/storage" ]; then
    echo -e "${YELLOW}⟳ Membuat storage link...${NC}"
    php artisan storage:link --quiet 2>/dev/null
    echo -e "${GREEN}✓${NC} Storage link dibuat"
fi

# Kill proses lama di port yang sama (jika ada)
if lsof -i :$PORT &> /dev/null; then
    echo -e "${YELLOW}⟳ Menghentikan server lama di port ${PORT}...${NC}"
    kill $(lsof -t -i :$PORT) 2>/dev/null
    sleep 1
fi

# Deteksi IP LAN
LAN_IP=$(hostname -I 2>/dev/null | awk '{print $1}')
if [ -z "$LAN_IP" ]; then
    LAN_IP=$(ip route get 1.1.1.1 2>/dev/null | awk '{print $7; exit}')
fi
if [ -z "$LAN_IP" ]; then
    LAN_IP="(tidak terdeteksi)"
fi

echo ""
echo -e "${GREEN}══════════════════════════════════════════════${NC}"
echo -e "  ${BOLD}Server berjalan di:${NC}"
echo -e ""
echo -e "  ${CYAN}➜  Lokal :${NC}  http://localhost:${PORT}"
echo -e "  ${CYAN}➜  LAN   :${NC}  http://${LAN_IP}:${PORT}"
echo -e ""
echo -e "  ${BOLD}Mode LAN aktif${NC} — perangkat lain di jaringan"
echo -e "  yang sama bisa mengakses via IP LAN di atas."
echo -e ""
echo -e "  ${BOLD}Login:${NC}"
echo -e "  ${CYAN}📧 Admin  :${NC} admin@pos.com  / password"
echo -e "  ${CYAN}📧 Kasir  :${NC} kasir@pos.com  / password"
echo -e "${GREEN}══════════════════════════════════════════════${NC}"
echo ""
echo -e "  Tekan ${YELLOW}Ctrl+C${NC} untuk menghentikan server"
echo ""

# Jalankan server (bind ke semua interface untuk LAN)
php artisan serve --host=$HOST --port=$PORT
