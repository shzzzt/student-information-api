# API Integration Map

All endpoint paths below are relative to `/api/v1`. Roles reflect the Laravel API middleware. `Instructor` is accepted by some backend routes even though the current frontend role type uses `faculty`.

| Frontend Page | Endpoint | Method | Role |
| --- | --- | --- | --- |
| Login | `/auth/login` | `POST` | Public |
| Application shell / session restore | `/auth/me` | `GET` | Authenticated |
| Application shell / sign out | `/auth/logout` | `POST` | Authenticated |
| Students list | `/students` | `GET` | Admin, Registrar |
| New student | `/students` | `POST` | Admin, Registrar |
| Student detail | `/students/{student}` | `GET` | Admin, Registrar; Student for own record |
| Edit student | `/students/{student}` | `PUT` | Admin, Registrar |
| Students list / deactivate action | `/students/{student}` | `DELETE` | Admin, Registrar |
| Programs list | `/programs` | `GET` | Admin, Registrar |
| New program | `/programs` | `POST` | Admin, Registrar |
| Edit program | `/programs/{program}` | `GET`, `PATCH` | Admin, Registrar |
| Programs list / delete action | `/programs/{program}` | `DELETE` | Admin, Registrar |
| Courses list | `/courses` | `GET` | Admin, Registrar |
| New course | `/courses` | `POST` | Admin, Registrar |
| Edit course | `/courses/{course}` | `GET`, `PATCH` | Admin, Registrar |
| Courses list / delete action | `/courses/{course}` | `DELETE` | Admin, Registrar |
| Academic terms list | `/academic-terms` | `GET` | Admin, Registrar |
| New academic term | `/academic-terms` | `POST` | Admin, Registrar |
| Edit academic term | `/academic-terms/{term}` | `GET`, `PATCH` | Admin, Registrar |
| Academic terms list / delete action | `/academic-terms/{term}` | `DELETE` | Admin, Registrar |
| Course offerings list | `/course-offerings` | `GET` | Admin, Registrar, Faculty, Instructor, Student |
| New course offering | `/course-offerings` | `POST` | Admin, Registrar |
| Edit course offering | `/course-offerings/{offering}` | `GET`, `PATCH` | Read: Admin, Registrar, Faculty, Instructor, Student; write: Admin, Registrar |
| Course offering students | `/course-offerings/{offering}/students` | `GET` | Admin, Registrar, Faculty, Instructor, Student, subject to record policy |
| Enrollments list | `/enrollments` | `GET` | Admin, Registrar, Faculty, Instructor, Student, subject to record policy |
| Enrollment detail | `/enrollments/{enrollment}` | `GET` | Admin, Registrar, Faculty, Instructor, Student, subject to record policy |
| Enrollment management | `/enrollments` | `POST` | Admin, Registrar |
| Enrollment management | `/enrollments/{enrollment}` | `PATCH`, `DELETE` | Admin, Registrar |
| Student detail / enrollments | `/students/{student}/enrollments` | `GET` | Admin, Registrar, Faculty, Instructor; Student for own record |
| Grades list | `/grades` | `GET` | Admin, Registrar, Faculty, Instructor, Student, subject to record policy |
| Grade detail | `/grades/{grade}` | `GET` | Admin, Registrar, Faculty, Instructor, Student, subject to record policy |
| Student detail / grades | `/students/{student}/grades` | `GET` | Admin, Registrar, Faculty, Instructor; Student for own record |
| Grades / enter grade | `/grades` | `POST` | Admin, Faculty, Instructor |
| Grades / edit grade | `/grades/{grade}` | `PATCH` | Admin; assigned Faculty or Instructor |
| Student detail / academic record | `/students/{student}/academic-record` | `GET` | Admin, Registrar, Faculty, Instructor; Student for own record |
| Offering form / instructor picker | `/users` | `GET` | Admin, Registrar |
| Profile | `/auth/me` | `GET` | Authenticated |

## Known route alignment notes

- The frontend currently permits `faculty` and `advisor` to open `/students`, while the backend students collection route permits only `admin`, `registrar`, and `student`; the controller then prevents students from listing all records. Align these role rules before enabling the page for faculty or advisors.
- The `/enrollments` frontend route is currently a placeholder. Enrollment data is already shown through nested student endpoints.
- Authentication uses JWT bearer tokens through the shared Axios request interceptor.
