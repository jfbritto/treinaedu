#!/bin/bash
set -e

# ============================================
# TreinaEdu - Script de Deploy
# ============================================
# Copie este arquivo para o servidor em:
# /home/deploy/deploy.sh
# E dê permissão: chmod +x /home/deploy/deploy.sh
# ============================================

APP_DIR="/home/deploy/treinaedu"
BRANCH="main"

echo "🚀 Iniciando deploy do TreinaEdu..."
echo "============================================"

cd "$APP_DIR"

# 1. Modo de manutenção (exibe 503 para visitantes)
echo "⏸  Ativando modo de manutenção..."
php artisan down --retry=30 --refresh=5 2>/dev/null || true

# 2. Puxar código mais recente
echo "📥 Atualizando código..."
git pull origin "$BRANCH"

# 3. Instalar dependências (sem dev, otimizado)
echo "📦 Instalando dependências..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Rodar migrations
echo "🗄  Rodando migrations..."
php artisan migrate --force

# 5. Limpar todos os caches
echo "🧹 Limpando caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 6. Rebuild dos caches otimizados
echo "⚡ Otimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Reiniciar queue workers (se usar)
# echo "🔄 Reiniciando workers..."
# php artisan queue:restart

# 8. Desativar modo de manutenção
echo "✅ Desativando modo de manutenção..."
php artisan up

echo "============================================"
echo "✅ Deploy concluído com sucesso!"
echo "============================================"
