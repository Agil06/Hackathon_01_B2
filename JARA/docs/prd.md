# Product Realization Document (PRD) — JARA

## 1. Product Behaviour Summary

Setelah implementasi, guest dapat mendaftar dan login. Pengguna reguler melihat hanya project tempat ia menjadi anggota, membuat project, menambah akun terdaftar sebagai collaborator, dan bersama seluruh member dapat mengubah/menghapus project serta CRUD task. Task memiliki priority, deadline opsional, dan status; halaman project menampilkan progress kategorikal hasil perhitungan task. Admin mengelola akun dari area terpisah. Semua mutasi tervalidasi di server dan semua akses project/task dilindungi membership.

Keputusan provisional yang harus dikonfirmasi PM: email adalah identitas login/collaborator, dan menghapus creator menghapus project miliknya (lihat BC-01/BC-02 di `srs.md`).

## 2. Feature Catalogue

### F-01 — Authentication

- **Requirement:** FR-01–FR-03; **Actor:** Guest / authenticated user.
- **Trigger:** register, login, logout.
- **Input:** name, email, password, password confirmation; atau email/password login.
- **Processing/output:** validasi; hash password; buat akun role `user`; `Auth::attempt`; kelola session; redirect sesuai contract.
- **Rules/error:** BR-01–BR-03; validation message; credential salah tidak membuat session.
- **Dependency:** Foundation F-00 (`users`, layout, routes skeleton).
- **Acceptance:** AC-01–AC-04.
- **Definition of Done:** seluruh tiga flow bekerja; guest/auth middleware benar; CSRF aktif; password hash; tests/smoke test lulus.

### F-02 — Admin User Management

- **Requirement:** FR-04–FR-06; **Actor:** Admin.
- **Trigger:** admin membuka list, submit create, atau delete.
- **Input:** name, email, password confirmation, role.
- **Processing/output:** list users; create hashed account; hard delete akun lain.
- **Rules/error:** BR-01–BR-05, BR-17; non-admin 403, self-delete ditolak.
- **Dependency:** F-00 dan F-01 untuk login.
- **Acceptance:** AC-05–AC-07.
- **Definition of Done:** routes admin terlindungi; list/create/delete persisted; duplicate/self-delete/non-admin paths teruji.

### F-03 — Project Management

- **Requirement:** FR-07–FR-11, FR-13, FR-21–FR-24; **Actor:** Regular project member.
- **Trigger:** project index/create/show/edit/update/delete.
- **Input:** project name.
- **Processing/output:** query project by membership; transaction membuat project+pivot creator; detail memuat tasks/members; computed progress; update/delete.
- **Rules/error:** BR-06–BR-08, BR-10, BR-16–BR-17; guest login redirect, non-member 403, missing 404.
- **Dependency:** F-00.
- **Acceptance:** AC-08–AC-12, AC-18–AC-20.
- **Definition of Done:** CRUD member-only berjalan, creator pivot konsisten, cascade benar, progress untuk seluruh kondisi benar.

### F-04 — Collaboration

- **Requirement:** FR-12–FR-13; **Actor:** Project member.
- **Trigger:** submit email pada detail project.
- **Input:** registered user email.
- **Processing/output:** cari exact email, cek membership, attach langsung, kembali ke detail dengan success.
- **Rules/error:** BR-08–BR-10; email tidak ditemukan/duplicate menampilkan error.
- **Dependency:** F-03 project/policy/detail view.
- **Acceptance:** AC-10, AC-13.
- **Definition of Done:** member dapat attach; non-member 403; missing/duplicate tidak membuat row; member baru segera memiliki akses setara.

### F-05 — Task Management

- **Requirement:** FR-14–FR-20; **Actor:** Project member.
- **Trigger:** create/show/edit/update/delete task.
- **Input:** title, priority, optional deadline; status hanya pada edit/update.
- **Processing/output:** validasi; simpan di project dari URL; redirect detail project; hard delete.
- **Rules/error:** BR-06, BR-10–BR-15, BR-17; nested task harus benar-benar milik project URL.
- **Dependency:** F-03 project/policy.
- **Acceptance:** AC-10, AC-14–AC-17, AC-20.
- **Definition of Done:** CRUD dan status transitions persisted; enum/date invalid ditolak; cross-project ID dan non-member ditolak; progress bereaksi.

### F-00 — Shared Foundation (enabler, bukan user feature)

- **Requirement:** FR-25 dan technical necessities.
- **Processing/output:** migration, model relationship skeleton, policy registration, shared layout, route naming skeleton, admin middleware, seeder.
- **Dependency:** tidak ada.
- **Acceptance:** AC-21 ditambah migration fresh berhasil.
- **Definition of Done:** `migrate:fresh --seed` berhasil; naming sama dengan `erd.md`/`design.md`; skeleton dapat dipakai semua branch.

## 3. Form Contracts

### FC-01 — Register (`POST /register`)

| Field | Label | Type | Required | Validation | Default/source |
|---|---|---|---:|---|---|
| `name` | Name | text | Yes | string, max:255 | old input |
| `email` | Email | email | Yes | email, max:255, unique:users,email | old input |
| `password` | Password | password | Yes | string, min:8, confirmed | none |
| `password_confirmation` | Confirm Password | password | Yes | matches password | none |

Success: akun `user` dibuat, redirect `login` dengan flash success.

### FC-02 — Login (`POST /login`)

| Field | Label | Type | Required | Validation | Default/source |
|---|---|---|---:|---|---|
| `email` | Email | email | Yes | email | old input |
| `password` | Password | password | Yes | string | none |

Success: regenerate session, redirect `projects.index`. Failure: kembali dengan error pada email dan hanya email diingat.

### FC-03 — Admin Create User (`POST /admin/users`)

Field `name`, `email`, `password`, `password_confirmation` mengikuti FC-01; `role` adalah required select dari `admin,user`, default `user`. Success redirect `admin.users.index`.

### FC-04 — Project Create/Update

| Field | Label | Type | Required | Validation | Default/source |
|---|---|---|---:|---|---|
| `name` | Project Name | text | Yes | string, max:255 | create: old input; edit: project name |

Create sukses menuju `projects.show`; update sukses kembali ke `projects.show`.

### FC-05 — Add Collaborator (`POST /projects/{project}/collaborators`)

| Field | Label | Type | Required | Validation | Default/source |
|---|---|---|---:|---|---|
| `email` | Collaborator Email | email | Yes | email; must exist in users; must not already be a project member | old input |

Success: attach dan kembali ke project detail. Tidak ada invitation form/status.

### FC-06 — Task Create (`POST /projects/{project}/tasks`)

| Field | Label | Type | Required | Validation | Default/source |
|---|---|---|---:|---|---|
| `title` | Task Title | text | Yes | string, max:255 | old input |
| `priority` | Priority | select | Yes | in:low,medium,high | `medium`; fixed options |
| `deadline` | Deadline | date | No | nullable,date | blank |

Status tidak ada di form create; server/database menetapkan `not_done`.

### FC-07 — Task Update (`PATCH /projects/{project}/tasks/{task}`)

Field FC-06 ditambah:

| Field | Label | Type | Required | Validation | Default/source |
|---|---|---|---:|---|---|
| `status` | Status | select | Yes | in:not_done,in_progress,done | current task; fixed options |

Success redirect `projects.show`. Semua status dapat dipilih dari status mana pun.

## 4. Screen / Page Contracts

| Page | Route | Required data | Actions | Destination / states |
|---|---|---|---|---|
| Register | `GET /register` | validation errors | submit, link login | success → login; auth user → projects |
| Login | `GET /login` | errors/flash | submit, link register | success → projects |
| Project List | `GET /projects` | member projects + computed progress | create, open, logout; admin link if role admin | empty: “No projects yet” |
| Project Create | `GET /projects/create` | errors | submit/cancel | success → project detail |
| Project Detail | `GET /projects/{project}` | project, creator, members, tasks, progress | edit/delete project, add collaborator, create/open/edit/delete task | no tasks/member error states; unauthorized 403 |
| Project Edit | `GET /projects/{project}/edit` | project, errors | update/cancel | success → detail |
| Task Create | `GET /projects/{project}/tasks/create` | project, priority options | submit/cancel | success → project detail |
| Task Detail | `GET /projects/{project}/tasks/{task}` | project + task | edit/back/delete | unauthorized 403, mismatch 404 |
| Task Edit | `GET /projects/{project}/tasks/{task}/edit` | project, task, enum options | update/cancel | success → project detail |
| Admin User List | `GET /admin/users` | all users | create/delete | empty impossible while current admin exists |
| Admin User Create | `GET /admin/users/create` | role options/errors | submit/cancel | success → user list |

Shared layout menampilkan flash success/error, validation summary, current name, logout, Projects, dan Admin Users hanya bila current role admin. Visual styling minimal.

## 5. Navigation / Interaction Flow

```text
Guest → Register → Login → Project List
                         ├→ Create Project → Project Detail
                         └→ Open Project → Project Detail
Project Detail ├→ Edit Project → Save → Project Detail
               ├→ Add Collaborator → Project Detail
               ├→ Create Task → Save → Project Detail
               ├→ Open/Edit Task → Save/Delete → Project Detail
               └→ Delete Project → Project List
Admin → Admin User List → Create User → Admin User List
                        └→ Delete User → Admin User List
Authenticated User → Logout → Login
```

## 6. Permission Matrix

| Action | Guest | Regular non-member | Project member (creator/collaborator) | Admin non-member | Admin member |
|---|---:|---:|---:|---:|---:|
| Register/login | Yes | N/A | N/A | N/A | N/A |
| View own member project list | No | Yes | Yes | Yes (may be empty) | Yes |
| Create project | No | Yes | Yes | No* | No* |
| View/update/delete project | No | No | Yes | No | No* |
| Add collaborator | No | No | Yes | No | No* |
| CRUD/change task | No | No | Yes | No | No* |
| View/create/delete users | No | No | No | Yes | Yes |

`*` Role design menganggap `admin` sebagai actor admin-only dan registrasi menghasilkan `user`. Jika PM ingin admin juga bertindak sebagai regular user, itu adalah perubahan behaviour yang harus dikunci sebelum coding. Admin tidak memperoleh bypass membership.

Creator dan collaborator berada pada kolom permission yang sama tanpa aksi eksklusif creator.

## 7. Traceability Matrix

| FR | Feature | Acceptance Criteria |
|---|---|---|
| FR-01 | F-01 | AC-01, AC-02 |
| FR-02 | F-01 | AC-03 |
| FR-03 | F-01 | AC-04 |
| FR-04 | F-02 | AC-05 |
| FR-05 | F-02 | AC-06 |
| FR-06 | F-02 | AC-07 |
| FR-07 | F-03 | AC-09 |
| FR-08 | F-03 | AC-08 |
| FR-09 | F-03 | AC-09 |
| FR-10 | F-03 | AC-11 |
| FR-11 | F-03 | AC-12 |
| FR-12 | F-04 | AC-13 |
| FR-13 | F-03/F-04/F-05 | AC-10 |
| FR-14 | F-05 | AC-14 |
| FR-15 | F-05 | AC-09 |
| FR-16 | F-05 | AC-15, AC-16 |
| FR-17 | F-05 | AC-17 |
| FR-18 | F-05 | AC-14 |
| FR-19 | F-05 | AC-16 |
| FR-20 | F-05 | AC-15, AC-20 |
| FR-21 | F-03 | AC-18 |
| FR-22 | F-03 | AC-19 |
| FR-23 | F-03 | AC-20 |
| FR-24 | F-03 | AC-12 |
| FR-25 | F-00 | AC-21 |

Tidak ada feature tanpa requirement; F-00 adalah technical enabler untuk FR-25.

## 8. Out of Scope

Invite/accept/reject, remove collaborator, task assignee, description, tag, comment, attachment, notification, search/filter/sort, password reset, profile edit, soft delete, audit log, progress percentage, API/SPA, dan UI framework tidak dibuat.
