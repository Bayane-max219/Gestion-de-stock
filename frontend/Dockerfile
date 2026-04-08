# ─────────────────────────────────────────────────────────────────────────────
# Stage 1 : Build — compiler le frontend Vue.js 3
# ─────────────────────────────────────────────────────────────────────────────
FROM node:20-alpine AS builder

WORKDIR /app

# Copier les fichiers de dépendances
COPY package.json package-lock.json ./

# Installer les dépendances
RUN npm ci --prefer-offline

# Copier tout le code source
COPY . .

# Construire pour la production
# VITE_API_URL sera injecté via docker-compose env ou .env
ENV VITE_API_URL=/api

RUN npm run build

# ─────────────────────────────────────────────────────────────────────────────
# Stage 2 : Servir avec Nginx
# ─────────────────────────────────────────────────────────────────────────────
FROM nginx:1.25-alpine AS production

# Copier le build Vue.js vers le répertoire Nginx
COPY --from=builder /app/dist /usr/share/nginx/html

# Configuration Nginx pour SPA (gestion des routes Vue Router)
COPY docker/nginx-spa.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
