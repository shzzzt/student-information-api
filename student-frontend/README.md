# Student Information Frontend

This project is the browser client for the Student Information API. It provides role-aware screens for authentication, student records, programs, courses, academic terms, course offerings, enrollments, grades, and academic records.

## Framework and libraries

- React 18 and TypeScript
- Vite 8
- React Router
- TanStack Query
- Axios
- React Hook Form and Zod
- Tailwind CSS
- Vitest and React Testing Library
- MSW for API mocks
- Playwright for end-to-end tests

## Prerequisites

- Node.js 20.19 or newer
- npm 10 or newer
- The Laravel API from the parent repository
- PHP 8.2+, Composer, and a supported database when running the API locally

## Install

From this directory:

```bash
npm install
```

Install the Playwright Chromium browser before the first end-to-end test run:

```bash
npx playwright install chromium
```

## Environment configuration

Create `student-frontend/.env.local`:

```dotenv
VITE_API_BASE_URL=http://127.0.0.1:8000/api/v1
```

`VITE_API_BASE_URL` is used by the Axios API client. Restart Vite after changing it.

The Laravel API must allow requests from `http://localhost:5173`. Configure its CORS settings deliberately. Never commit `.env.local`, access tokens, or production credentials.

### Authentication integration note

The frontend uses the Laravel JWT endpoints at `/api/v1/auth/login`, `/api/v1/auth/me`, and `/api/v1/auth/logout`. Login stores the returned token in `localStorage` under `auth_token`; the shared Axios client then adds `Authorization: Bearer <token>` to API requests. A `401` clears the token and returns the user to login, except for the initial `/auth/me` session check.

The current unit and Playwright suites mock this boundary, while live integration can be verified by running Laravel and Vite together.

## Run locally

Start the Laravel API from the repository root:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Then start the frontend from `student-frontend`:

```bash
npm run dev
```

Open `http://localhost:5173`.

## Build and preview

```bash
npm run build
npm run preview
```

The production build is written to `dist/`.

## Tests and code quality

```bash
# Unit and component tests
npm test

# Unit tests in watch mode
npm run test:watch

# Playwright E2E tests; starts Vite automatically
npm run test:e2e

# Lint the project
npm run lint

# Type-check and create a production build
npm run build
```

The E2E tests intercept API calls to keep authentication and test data deterministic. They do not modify the local database.

## API connection

The API is expected at `http://127.0.0.1:8000/api/v1`. API modules live in `src/api`, server-state hooks live in `src/hooks`, and the shared Axios instance is `src/api/client.ts`.

List pages send pagination and filter values as query parameters. Successful resource responses generally use this envelope:

```json
{
  "success": true,
  "message": "Resource retrieved successfully.",
  "data": {},
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 1,
    "last_page": 1
  }
}
```

See [API_INTEGRATION_MAP.md](API_INTEGRATION_MAP.md) for the page-to-endpoint mapping.

## Seeded test accounts

Run the backend seeders before using these local accounts:

```bash
php artisan migrate:fresh --seed
```

All seeded accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@example.com` |
| Registrar | `registrar@example.com` |
| Faculty | `faculty@example.com` |
| Student | `student@example.com` |

These credentials are for local development only. Change or remove them before deploying the application.
