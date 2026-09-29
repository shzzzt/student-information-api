<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

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

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## Student API setup

Install dependencies and configure the application:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Set these PostgreSQL values in `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=student_information_api
DB_USERNAME=student_api_user
DB_PASSWORD=your_password
```

Run the database, generate the OpenAPI document, and start Laravel:

```powershell
php artisan migrate
php artisan l5-swagger:generate
php artisan serve
```

Swagger UI: `http://localhost:8000/api/docs` (or `http://localhost:8000/api/documentation`, which redirects to the same page)

JWT endpoints:

- `POST /api/v1/auth/login` with `email` and `password`
- `GET /api/v1/auth/me` with a bearer token
- `POST /api/v1/auth/logout` with a bearer token

Use `Authorization: Bearer <access_token>` for protected endpoints. Passwords must be stored as bcrypt hashes; the application uses the `BCRYPT_ROUNDS` setting when creating hashes.

## CRUD resources

All resource endpoints require a bearer JWT and return `{ success, message, data }`.

- `/api/v1/students`: list, create, retrieve, update, and deactivate students. List supports `search`, `program_id`, `year_level`, `status`, `sort`, `direction`, and `per_page`.
- `/api/v1/programs`: list, create, retrieve, update, and delete programs. List supports `search`, `degree_level`, `status`, `sort`, `direction`, and `per_page`.
- `/api/v1/courses`: list, create, retrieve, update, and delete courses. List supports `search`, `program_id`, `status`, `sort`, `direction`, and `per_page`.
- `/api/v1/academic-terms`: list, create, retrieve, update, and delete academic terms. List supports `search`, `term_type`, `academic_year`, `status`, `sort`, `direction`, and `per_page`.
- `/api/v1/course-offerings`: standard CRUD for course, academic-term, and instructor links. `capacity` must be greater than zero; instructors use the existing `faculty` user role.
- `/api/v1/enrollments`: list, create, retrieve, patch, and delete enrollments. Duplicate student/offering pairs return `409 Conflict`.
- `/api/v1/grades`: list, create, retrieve, and update grades. Numeric grades use the `0-100` range and only the assigned offering instructor can create or update them.
- `/api/v1/students/{id}/enrollments`, `/api/v1/students/{id}/grades`, and `/api/v1/course-offerings/{id}/students`: nested enrollment and grade lookups.
- `/api/v1/students/{id}/academic-record`: returns enrollments grouped by academic term with grades attached.

Validation failures use `{ success: false, errors: { field: [messages] } }`.

## Authorization

All API resources require a JWT. Role middleware and object-level checks enforce:

- `admin`: full access.
- `registrar`: manages students, programs, courses, terms, offerings, and enrollments.
- `faculty`: views only assigned course offerings and manages grades only for those offerings. The existing `faculty` database role is the instructor role.
- `student`: views only the linked student profile, enrollments, grades, and academic record. Changing the student ID in the URL returns `403`.

Missing route-model resources return `404`; authenticated but unauthorized access returns `403`.

## Project setup

This repository contains the Laravel REST API for a Student Information Management system. It uses PostgreSQL, JWT authentication, OpenAPI/Swagger, and Vite-managed frontend assets.

### Prerequisites

- PHP 8.3 or newer with the PostgreSQL extension enabled
- Composer 2
- PostgreSQL 14 or newer
- Node.js 20 or newer and npm

### Install

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan jwt:secret
npm install
```

Configure `.env` with a PostgreSQL database that already exists:

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=student_information_api
DB_USERNAME=student_api_user
DB_PASSWORD=your_local_password
JWT_TTL=60
BCRYPT_ROUNDS=12
```

`.env.example` contains placeholders only. Do not commit `.env`, `APP_KEY`, `JWT_SECRET`, or database passwords.

### Database and seed data

```powershell
php artisan migrate
php artisan db:seed
```

For a clean local database:

```powershell
php artisan migrate:fresh --seed
```

### Run the application

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

The API is available at `http://127.0.0.1:8000/api/v1`. Generate frontend assets when needed with `npm run build`; use `npm run dev` for Vite development.

### Run the frontend client

In a second PowerShell terminal:

```powershell
Set-Location student-frontend
npm install
Copy-Item .env.example .env
npm run dev
```

The frontend reads `VITE_API_BASE_URL` from `student-frontend/.env`; the default points to the Laravel API above. Keep the local `.env` file private and do not commit it.

### Frontend checks

From `student-frontend/`, run:

```powershell
npm run lint
npm run build
npm test
```

The Playwright tests under `tests/e2e/` exercise the running Laravel API without API mocks. Start Laravel in another terminal and use seeded local test data, then enable the live checks in the frontend terminal:

```powershell
$env:E2E_API_TESTS = "1"
$env:E2E_EMAIL = "admin@example.com"
$env:E2E_PASSWORD = "password"
npx playwright install chromium
npm run test:e2e
```

The real API smoke tests cover successful login and live dashboard counts, invalid credentials, and mobile navigation to the live enrollment list. Store screenshots and the Playwright report in the submission evidence folder after running them against the local seeded API.

### Authentication

1. Call `POST /api/v1/auth/login` with an email and password.
2. Copy `access_token` from the response.
3. Send `Authorization: Bearer <access_token>` to protected endpoints.
4. Call `POST /api/v1/auth/logout` to revoke the token.

Seeded local accounts all use the password `password`:

| Role | Email |
| --- | --- |
| Admin | `admin@example.com` |
| Registrar | `registrar@example.com` |
| Faculty/instructor | `faculty@example.com` |
| Student | `student@example.com` |

Change or remove seeded credentials before using the application outside local development.

### Swagger/OpenAPI

Generate the specification after changing OpenAPI attributes:

```powershell
php artisan l5-swagger:generate
```

Open Swagger UI at `http://127.0.0.1:8000/api/docs` (`/api/documentation` redirects to the same page and the raw specification is served from `/docs`).

The specification covers every endpoint group: authentication, students, programs, courses, academic terms, course offerings, enrollments (including `/students/{id}/enrollments` and `/course-offerings/{id}/students`), grades (including `/students/{id}/grades`), the academic record, and the user directory. Annotations live in `app/Docs/`, one class per endpoint group, together with the shared request, resource, and response schemas referenced by `#/components/schemas`.

### Tests

```powershell
php artisan test
```

The test suite uses the SQLite in-memory database configured in `phpunit.xml` and refreshes migrations between tests. It covers authentication, CRUD, validation, authorization, duplicate enrollment conflicts, grade permissions, collections, search, filtering, sorting, and pagination.

### API collection and ERD

- Import [docs/postman/student-information-api.postman_collection.json](docs/postman/student-information-api.postman_collection.json) into Postman or Insomnia. Run **Authentication > Login** first and set the returned token in the collection variable.
- Copy [docs/erd.dbml](docs/erd.dbml) into [dbdiagram.io](https://dbdiagram.io/) to render the database relationship diagram.
- Review [docs/ai-development-log.md](docs/ai-development-log.md) for the task, tool, result, human-review, and evidence history.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
