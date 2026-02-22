<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

## Despliegue con Docker

Este proyecto incluye `Dockerfile` y `docker-compose.yml` para ejecutar app + MySQL en contenedores con inicialización automática (migraciones + seed en el primer arranque).

### Requisito previo

Si ves `docker: command not found`, instala y abre Docker Desktop antes de continuar.

### Levantar el entorno

```bash
docker compose up --build -d
```

Con ese único comando se crea la imagen, se levantan contenedores, se espera a MySQL, se ejecutan migraciones y seed inicial, y queda la app funcionando.

La aplicación quedará en:

- `http://localhost:8000`

MySQL quedará expuesto en:

- Host: `127.0.0.1`
- Puerto: `3307`
- BD: `storybook`
- Usuario: `storybook`
- Password: `storybook`

### Comandos útiles

```bash
# Ver logs
docker compose logs -f app

# Ejecutar seed manualmente
docker compose exec app php artisan db:seed --force

# Parar contenedores
docker compose down

# Reiniciar desde cero (borra BD y vuelve a sembrar al levantar)
docker compose down -v
docker compose up --build -d
```

### Modo producción (para compartir)

Usa `docker-compose.prod.yml` para levantar el proyecto sin exponer MySQL hacia fuera.

1. Crea archivo de variables:

```bash
cp .env.example .env.docker.prod
```

2. Define variables mínimas (ejemplo):

PowerShell (Windows):

```powershell
$env:APP_URL="http://localhost:8000"
$env:APP_PORT="8000"
$env:DB_DATABASE="storybook"
$env:DB_USERNAME="storybook"
$env:DB_PASSWORD="storybook"
$env:DB_ROOT_PASSWORD="change_this_root_password"
```

Bash (Linux/macOS):

```bash
export APP_URL=http://localhost:8000
export APP_PORT=8000
export DB_DATABASE=storybook
export DB_USERNAME=storybook
export DB_PASSWORD=storybook
export DB_ROOT_PASSWORD=change_this_root_password
```

3. Levanta en modo producción:

```bash
docker compose -f docker-compose.prod.yml up --build -d
```

4. Compartir imagen por Docker Hub:

```bash
docker tag storybook_laravel2-app:latest TU_USUARIO/storybook-laravel2:1.0.0
docker push TU_USUARIO/storybook-laravel2:1.0.0
```

5. Compartir imagen por archivo:

```bash
docker save storybook_laravel2-app:latest -o storybook-laravel2.tar
```

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
