#!/bin/bash

echo "=========================================="
echo "  ROMS AUTOMATED SETUP (Mac/Linux)"
echo "=========================================="
echo ""

echo "[1/5] Copying .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env created."
else
    echo ".env already exists. Skipped."
fi

echo ""
echo "[2/5] Installing PHP Dependencies..."
composer install

echo ""
echo "[3/5] Generating App Key..."
php artisan key:generate

echo ""
echo "[4/5] Installing Node Dependencies..."
npm install
npm run build

echo ""
echo "[5/5] Setting up Database..."
php artisan migrate

echo ""
read -p "Apakah ingin mengisi database dengan data awal (Seed)? (y/n): " seed
if [[ "$seed" == "y" || "$seed" == "Y" ]]; then
    echo "Seeding database..."
    php artisan db:seed
fi

echo ""
echo "=========================================="
echo "  SETUP COMPLETE! SIAP DIGUNAKAN 🚀"
echo "  Jalankan: php artisan serve"
echo "=========================================="
