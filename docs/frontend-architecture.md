# Frontend Architecture

## Runtime flow

The React/TypeScript application is a separate client under `student-frontend/`. React Router renders public and authenticated pages. `AuthContext` restores the current user from the bearer token, while `ProtectedRoute` keeps unauthenticated users out of the application shell and sends disallowed roles to the forbidden page.

Pages call resource hooks. Hooks use TanStack Query for request state, caching, invalidation, and pagination. Resource API modules make HTTP requests through `src/api/client.ts`; the API base URL comes from `VITE_API_BASE_URL`. The Laravel API remains responsible for authentication, validation, authorization, and persistence. The browser does not connect directly to PostgreSQL.

```text
React Router → Pages → TanStack Query hooks → Resource API modules → Axios client
                                                                    ↓
                                                    Laravel /api/v1 → PostgreSQL
```

## Authentication and error handling

- `AuthContext` calls `/auth/login`, `/auth/me`, and `/auth/logout`.
- The Axios client attaches the bearer token and clears expired credentials after `401` responses.
- `403` responses navigate to the forbidden page; forms use the shared error formatter for `409`, `422`, server errors, and network failures.
- Route and navigation checks improve the user experience. The backend remains the security boundary.

## Data ownership and role-aware screens

Dashboard counts and records are loaded from the existing API. The enrollment page calls `GET /enrollments` with supported query parameters and uses `POST`, `PATCH`, and `DELETE` only for administrator and registrar accounts. Grade creation uses the existing `POST /grades` endpoint for administrators and assigned instructors. Student list access is shown only to administrator and registrar accounts because the API explicitly rejects student collection listing and does not permit faculty collection listing.

The backend also defines an `advisor` role for several collection routes, but its current service methods do not consistently scope those collections to an advisor's assigned records. The frontend therefore does not expose those data screens to advisor accounts until the API contract provides safe scoping.

## Responsive behavior

The desktop navigation uses a fixed sidebar. At small viewport widths, the sidebar is replaced by a keyboard-accessible drawer with a backdrop; page content uses responsive padding and horizontally scrollable data tables.

## Local verification

From `student-frontend/`:

```powershell
npm run lint
npm run build
npm test
npm run test:e2e
```

Real API E2E coverage is opt-in. Start Laravel and Vite separately, seed a local development database, then set `E2E_API_TESTS=1`, `E2E_EMAIL`, and `E2E_PASSWORD` in the local shell before running Playwright. The real API tests do not use `page.route()` or mocked responses.
