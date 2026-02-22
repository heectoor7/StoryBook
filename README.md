# Despliegue con Docker

Este proyecto se ejecuta con **Docker** y **Docker Compose**.
Docker construye y ejecuta los contenedores, y Docker Compose orquesta los servicios (app + base de datos).

## Primer arranque

```bash
docker compose up --build -d
```

Este comando:
- `up`: crea e inicia los servicios definidos en `docker-compose.yml`.
- `--build`: reconstruye la imagen de la aplicación antes de iniciar.
- `-d`: deja los contenedores corriendo en segundo plano.

## Siguientes arranques

```bash
docker compose up -d
```

Este comando vuelve a iniciar los contenedores usando la imagen ya construida (sin reconstruir), también en segundo plano.

## URL de la aplicación

Usamos el puerto `18765` porque es poco común y así evitamos choques con otros servicios locales.

http://localhost:18765
