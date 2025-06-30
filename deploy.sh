#!/bin/bash

# Garante que o script pare se algum comando falhar
set -e

echo "Iniciando deploy em $(date)"

# Navega para o diretório da aplicação
# O script será executado a partir daqui, então o caminho é relativo
echo "Entrando no diretório da aplicação..."
# (Não precisa de 'cd', pois vamos configurar o diretório de trabalho no docker-compose)

# Puxa as últimas alterações da branch 'develop'
echo "Baixando atualizações do Git..."
git pull origin develop

# Reconstrói e reinicia os containers com as novas alterações
# O '--build' é crucial para construir o novo código nas imagens
echo "Reconstruindo e reiniciando os containers Docker..."
docker compose -f docker-compose.dev.yml up -d --build

echo "Deploy concluído com sucesso!"