# frontend/Dockerfile

# --- Estágio 1: Build (O Construtor) ---
# Usamos uma imagem oficial do Node.js para construir nosso projeto Vue
FROM node:22-alpine AS builder

# Define o diretório de trabalho dentro do container
WORKDIR /app

# Copia os arquivos de dependência e instala elas primeiro
# Isso aproveita o cache do Docker. Se as dependências não mudarem,
# o 'npm ci' não precisa rodar de novo em builds futuros.
COPY package*.json ./
RUN npm ci

# Copia o resto do código da aplicação
COPY . .

# Roda o script de build definido no seu package.json
# Isso vai gerar a pasta /app/dist com os arquivos estáticos
RUN npm run build


# --- Estágio 2: Produção (O Servidor Final) ---
# Usamos uma imagem super leve do Nginx para servir os arquivos estáticos
FROM nginx:stable-alpine

# Copia SOMENTE a pasta 'dist' (gerada no estágio anterior)
# para a pasta padrão do Nginx que serve os arquivos web.
COPY --from=builder /app/dist /usr/share/nginx/html

# Copia o nosso arquivo de configuração do Nginx para o local correto dentro do container
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expõe a porta 80, que é a porta padrão do Nginx
EXPOSE 80

# Comando para iniciar o servidor Nginx
CMD ["nginx", "-g", "daemon off;"]