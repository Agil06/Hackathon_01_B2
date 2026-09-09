# Pembagian Kerja Programmer — JARA

## Aturan Bersama

- Branch utama: `main`; branch fitur: `feature/<nama-fitur>`.
- PM mengunci schema, route, field, enum, dan naming sebelum coding.
- Programmer hanya mengubah file dalam scope-nya.
- Nilai tetap: role `admin|user`, priority `low|medium|high`, status `not_done|in_progress|done`.
- Shared files (`routes/web.php`, migrations, models, policy, layout) hanya diubah oleh **al**.

## Assignment Berbasis Fitur

| Programmer | Fitur yang direalisasikan | Requirement | Branch | Hasil akhir yang harus terlihat |
|---|---|---|---|---|
| **al** | Project Management + Project Progress | FR-07–11, FR-21–25 | `feature/shared-foundation`, lalu `feature/projects` | User dapat membuat, melihat, mengubah, dan menghapus project; progress menampilkan `Not Started`, `In Progress`, atau `Completed` secara benar. |
| **galang** | Authentication + Task Management | FR-01–03, FR-14–20 | `feature/authentication`, lalu `feature/tasks` | Register/login/logout berfungsi; member dapat CRUD task, memilih priority/deadline, dan mengubah status dua arah. |
| **daniel** | Admin User Management | FR-04–06 | `feature/admin-users` | Admin dapat melihat, membuat, dan menghapus permanen akun; regular user ditolak; admin tidak dapat menghapus diri sendiri. |
| **abhi** | Project Collaboration + Membership Authorization | FR-12–13 | `feature/collaboration` | Member dapat menambahkan akun terdaftar melalui email; collaborator langsung mendapat akses setara; non-member mendapat 403. |

## Detail Fitur per Programmer

### al — Project Management & Progress

- Membuat shared foundation: migration, model relationship, routes, policy, layout, dan admin seeder.
- Merealisasikan halaman daftar, create, detail, edit, dan delete project.
- Creator otomatis menjadi member saat project dibuat.
- Progress dihitung dari status task dan tidak disimpan di database.
- **DoD:** migration dapat direproduksi; project CRUD member-only; cascade delete dan tiga kondisi progress lulus.

### galang — Authentication & Task Management

- Merealisasikan register, login, dan logout berbasis session.
- Merealisasikan halaman create, detail, edit, dan delete task.
- Task baru berstatus `not_done`; priority hanya tiga nilai; deadline opsional.
- Semua perubahan status `not_done`, `in_progress`, dan `done` diperbolehkan.
- **DoD:** auth dan task CRUD berjalan; input invalid ditolak; task lintas project/non-member tidak dapat diakses.

### daniel — Admin User Management

- Merealisasikan halaman daftar dan form pembuatan akun.
- Admin dapat membuat akun role `admin` atau `user`.
- Admin dapat hard-delete akun lain, tetapi tidak akun sendiri.
- **DoD:** operasi admin persisted; duplicate email ditolak; regular user memperoleh 403; email dapat dipakai lagi setelah akun dihapus.

### abhi — Collaboration & Membership Authorization

- Merealisasikan form penambahan collaborator berdasarkan email.
- Akun yang ditemukan langsung ditambahkan tanpa invitation/approval.
- Menolak email tidak terdaftar dan membership duplicate.
- Memastikan creator dan collaborator mempunyai akses project/task yang sama.
- **DoD:** collaborator langsung melihat dan mengelola project; non-member tidak dapat melihat atau memanipulasi project/task.

## File Ownership

- **al:** `routes/web.php`, `database/*`, `app/Models/*`, `app/Policies/*`, middleware admin, shared layout, `ProjectController`, `views/projects/index|create|edit|show`.
- **galang:** `AuthController`, `views/auth/*`, `TaskController`, `views/tasks/*`, `views/projects/_task-list.blade.php`.
- **daniel:** `AdminUserController`, `views/admin/users/*`.
- **abhi:** `CollaboratorController`, `views/projects/_collaborator-form.blade.php`.

## Urutan Kerja dan Merge

1. **al** menyelesaikan dan PM merge `feature/shared-foundation`.
2. Semua programmer membuat branch baru dari `main` terbaru.
3. Authentication, Admin Users, Projects, dan Collaboration dikerjakan paralel.
4. Setelah Authentication selesai, **galang** mengerjakan `feature/tasks` dari `main`, bukan dari branch authentication.
5. Urutan merge: `shared-foundation` → `authentication` → `admin-users` → `projects` → `collaboration` → `tasks`.

## Checklist Sebelum Push

- [ ] Scope dan file sesuai assignment.
- [ ] Tidak mengubah contract tanpa persetujuan PM.
- [ ] Validation, authorization, redirect, dan persistence sudah diuji.
- [ ] Tidak ada error pada fitur terkait.
- [ ] Commit jelas dan branch sudah di-push untuk review PM.
