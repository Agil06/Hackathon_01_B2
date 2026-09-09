# JARA — Advanced To-Do List

JARA adalah aplikasi Laravel + MySQL untuk mengelola project dan task secara pribadi maupun bersama collaborator. Creator dan collaborator memiliki hak pengelolaan yang sama di project. Admin Sistem mempunyai area khusus untuk membuat dan menghapus akun.

Dokumen implementasi:

- [`srs.md`](srs.md) — requirement, business rules, acceptance criteria.
- [`prd.md`](prd.md) — feature, form/page contract, permission, traceability.
- [`erd.md`](erd.md) — schema, relationship, migration/seeder plan.
- [`design.md`](design.md) — route/component contract, assignment, integration, tests.
- [`howToExecute.md`](howToExecute.md) — panduan menjalankan aplikasi secara lokal.

## Prerequisites

- PHP sesuai versi yang diminta `composer.json` (disarankan PHP 8.2+ untuk Laravel modern).
- Composer.
- MySQL lokal dan satu database kosong.
- Node.js/npm hanya jika baseline Laravel memakai asset build Vite.
- Git.

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Pada Windows PowerShell, pengganti perintah copy:

```powershell
Copy-Item .env.example .env
```

Isi konfigurasi MySQL pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jara
DB_USERNAME=root
DB_PASSWORD=
```

Lanjutkan:

```bash
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`. Jika repository memakai Vite/CSS build, jalankan `npm install` lalu `npm run dev` pada terminal kedua; jangan jalankan bila tidak ada dependency frontend.

### Sample Admin Lokal

- Email: `admin@jara.test`
- Password: `password`

Credential ini hanya untuk praktikum lokal. Jangan gunakan pada deployment publik.

## Project Structure

```text
app/
  Http/Controllers/       # Auth, admin user, project, collaborator, task
  Http/Middleware/        # Admin role guard
  Models/                 # User, Project, Task
  Policies/               # Project membership authorization
database/
  migrations/             # Reproducible MySQL schema
  seeders/                # Development admin
resources/views/
  layouts/ auth/ admin/ projects/ tasks/
routes/web.php            # Locked named web routes
tests/Feature/            # Behaviour/authorization tests jika dibuat
```

## Git Workflow

- `main` adalah integration branch.
- Satu cohesive unit memakai `feature/<nama-fitur>`.
- Foundation harus merge dahulu; seluruh feature branch dimulai dari baseline `main` yang sama setelah foundation.
- Programmer hanya mengubah files dalam ownership branch pada `design.md`; perubahan shared contract diajukan ke PM.
- Commit kecil dan jelas, jalankan smoke test, lalu push setelah Definition of Done tercapai.
- PM review scope/diff, merge, menjalankan migration/tests, dan smoke test setelah setiap merge.

Contoh:

```bash
git checkout main
git pull
git checkout -b feature/authentication
git add <file-dalam-scope>
git commit -m "Implement registration login and logout"
git push -u origin feature/authentication
```

## Quick Implementation Board

| Feature | Requirement | Programmer | Branch | Dependency | Status | Definition of Done |
|---|---|---|---|---|---|---|
| Shared Foundation | FR-25 + technical necessity | al | `feature/shared-foundation` | Laravel baseline | TODO | migrate fresh+seed, routes/models/policy/layout boot |
| Authentication | FR-01–03 | galang | `feature/authentication` | Foundation | TODO | register/login/logout + errors lulus |
| Admin Users | FR-04–06 | daniel | `feature/admin-users` | Foundation; login untuk smoke | TODO | list/create/delete, 403, self-delete guard lulus |
| Projects & Progress | FR-07–11,13,21–24 | al | `feature/projects` | Foundation | TODO | CRUD member-only, creator pivot, cascade, progress lulus |
| Collaboration | FR-12–13 | abhi | `feature/collaboration` | Foundation; integrate after Projects | TODO | attach direct, missing/duplicate/403 lulus |
| Tasks | FR-14–20 | galang setelah auth | `feature/tasks` | Foundation; integrate after Projects | TODO | nested CRUD/status/validation/auth lulus |

Status sederhana: `TODO` → `IN PROGRESS` → `READY TO MERGE` → `DONE`; gunakan `BLOCKED` hanya bila contract/dependency belum tersedia.

## Integration Checklist for PM

- [ ] Putuskan BC-01 dan BC-02 di `srs.md`, lalu freeze schema/route/naming.
- [ ] Merge foundation pertama dan pastikan semua feature branch berasal dari baseline tersebut.
- [ ] Periksa file diff terhadap ownership branch.
- [ ] Merge urutan: authentication → admin users → projects → collaboration → tasks.
- [ ] Setelah setiap merge: install dependency bila berubah, `php artisan migrate`, `php artisan test`, dan smoke test fitur.
- [ ] Pastikan hanya owner yang mengubah routes, migrations, models, policy, shared layout, dan project detail shell.
- [ ] Jalankan `php artisan migrate:fresh --seed` dan acceptance checklist final.

## Final Submission Checklist

- [ ] Seluruh FR-01–FR-25 terpetakan dan tidak ada GAP.
- [ ] Register/login/logout, admin, project, collaboration, task, status, progress berfungsi.
- [ ] Non-member mendapat 403; missing/mismatched resource aman.
- [ ] Hard delete dan cascade sesuai keputusan PM.
- [ ] Database dapat dibangun dari migration/seeder tanpa phpMyAdmin manual.
- [ ] `.env`, credential asli, `vendor/`, dan dependency lokal tidak di-commit.
- [ ] README/how-to sesuai keadaan repository final.
- [ ] Tidak ada fitur/package/schema/route di luar contract.

## Scope Boundaries

Tidak termasuk: invitation/approval collaborator, remove collaborator, password reset, profile, soft delete, notification, komentar/attachment task, persentase progress, REST API, SPA, atau role khusus project.
