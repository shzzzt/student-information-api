# Frontend API Integration Map

All requests use the configured `VITE_API_BASE_URL` (normally `http://localhost:8000/api/v1`) and the current bearer token. API validation and authorization remain authoritative.

| Screen | API endpoint(s) | Frontend behavior |
| --- | --- | --- |
| Login / session | `POST /auth/login`, `GET /auth/me`, `POST /auth/logout` | Sign in, restore current user, clear token on logout or `401`. |
| Dashboard | `GET /students`, `/programs`, `/courses`, `/academic-terms`, `/course-offerings`, `/enrollments`, `/grades` | Load role-appropriate totals from live paginated responses; show loading, retry, and error states. |
| Students | `GET /students` with search, program, year, status, sort, page; `GET/POST/PUT/DELETE /students/{id}` | List and maintain student records for admin/registrar; individual record visibility remains subject to backend checks. |
| Programs | `GET/POST/GET/PATCH/DELETE /programs` | Manage records for admin/registrar. |
| Courses | `GET/POST/GET/PATCH/DELETE /courses` | Manage records for admin/registrar. |
| Academic terms | `GET/POST/GET/PATCH/DELETE /academic-terms` | Manage terms for admin/registrar. |
| Course offerings | `GET /course-offerings`; `POST/PATCH/DELETE` for administrators and registrars | List available offerings; management actions only appear for roles permitted by the API. |
| Enrollments | `GET /enrollments` with student, offering, status, sort, and pagination; `POST/PATCH/DELETE /enrollments` | Browse and filter records; create, change status, and delete only for admin/registrar. Duplicate enrollment `409` and validation `422` are shown to the user. |
| Grades | `GET /grades`; `POST /grades`; `PATCH /grades/{id}` | View grades and record/update grades through the API for admins and assigned instructors. Backend checks instructor ownership. |
| Student academic record | `GET /students/{id}/enrollments`, `/grades`, `/academic-record` | Present term-grouped enrollment and grade details; backend checks record ownership. |

## Collection query format

The Laravel paginator is nested in the API response `data` property. Resource clients normalize its `current_page`, `per_page`, `total`, and `last_page` values into a shared page shape for tables and pagination controls. The student collection endpoint returns its page metadata at the top level and is handled using its documented response shape.

## Role limitations found during frontend review

The API's `advisor` role is allowed through some collection routes, while the corresponding service methods do not consistently limit results to advisor-assigned records. The frontend currently hides those screens for advisors. To enable them safely, the API must provide a documented scope before the frontend displays those records.
