# Pembagian Kerja Programmer JARA

Pembagian ini memaksimalkan pekerjaan paralel. Foundation harus selesai dan di-merge lebih dahulu karena semua fitur bergantung pada schema, relasi, middleware, dan naming yang sama. Setelah itu, empat paket dapat dikerjakan terpisah dengan kontrak berikut.

## Kontrak Bersama yang Tidak Boleh Diubah Sepihak

- Role: `admin|user`; priority: `low|medium|high`; status: `not_done|in_progress|done`.
- Gunakan istilah UI **Daftar Tugas**. Jika kode lama memakai `Project`, nama model/tabel boleh tetap `Project`/`projects`, tetapi kontrak relasi tidak berubah.
- `owner_id` adalah pemilik daftar; `list_user`/`project_user` adalah membership unik; `task_user` adalah assignment unik.
- Semua query menggunakan Eloquent/query builder atau prepared statement parameterized. Semua proses multi-row memakai `DB::transaction`.
- Setiap feature branch hanya mengubah file yang menjadi miliknya. Perubahan shared contract diajukan sebagai PR terpisah kepada Al.

## Pembagian Utama

| Programmer | Paket mandiri setelah foundation | Requirement | Branch | Dependency |
|---|---|---|---|---|
| **Al** | Foundation, daftar tugas, ownership, membership management | FR-04–09, FR-16–17 | `feature/foundation-lists` | Tidak ada; menjadi baseline tim. |
| **Agil** | Authentication dan admin account management | FR-01–03 | `feature/auth-admin` | `users`, role middleware, dan shared layout dari foundation. |
| **Galang** | Task CRUD, status, prioritas, deadline, progress presentation | FR-10–13, FR-16 | `feature/tasks` | Model/list policy dari foundation. |
| **Abhi** | Multi-assignee task dan halaman tugas saya | FR-14–15 | `feature/task-assignees` | Task CRUD Galang sudah di-merge; membership/list policy dari foundation. |

## Detail Scope dan Definition of Done

### Al - Foundation Daftar dan Membership

- Membuat migration/relasi `users`, `lists`/`projects`, `list_user`/`project_user`, `tasks`, dan `task_user`, termasuk FK serta unique index.
- Membuat `ListPolicy`/`ProjectPolicy`, middleware role admin, seeder admin, layout dasar, route naming skeleton, dan test support.
- Membuat daftar: index, create, show, edit, update, delete; create list + owner membership wajib dalam transaction.
- Membuat tambah/hapus anggota oleh owner; owner tidak dapat dihapus; hapus daftar meng-cascade seluruh data turunan.
- Menyediakan partial/contract halaman detail agar Galang dan Abhi dapat memasang fitur tanpa mengubah controller/list view utama.
- **DoD:** migration fresh berhasil; owner/member/non-member berbeda haknya; rollback pembuatan daftar terbukti; membership duplicate/owner removal ditolak.

### Agil - Authentication dan Administrasi Akun

- Membuat register, login, logout, session regeneration/invalidation, dan guest/auth middleware integration.
- Membuat area admin list/create/delete account, role validation, password hashing, dan larangan self-delete.
- Tidak mengubah schema/route shared tanpa persetujuan Al; gunakan route slot yang disediakan foundation.
- **DoD:** register/login/logout; password tidak plaintext; admin-only area 403 untuk user; duplicate email dan self-delete ditolak; feature tests lulus.

### Galang - Task Management

- Membuat create, read, update, delete, dan mark-done untuk task bersarang dalam daftar.
- Validasi title, priority, deadline, status; task baru `not_done`; pastikan task benar milik daftar URL dan actor adalah member.
- Menyajikan progres daftar dari task yang dibaca, tanpa menyimpan kolom progress.
- Expose hook data assignee (misalnya `assigned_user_ids`) untuk Abhi, tetapi tidak mengelola tabel `task_user`.
- **DoD:** task CRUD member-only; enum/date invalid dan cross-list task ditolak; tiga kondisi progress benar; delete task menghapus assignment lewat FK.

### Abhi - Multi Assignee dan Tugas Saya

- Menggunakan migration dan relasi `task_user` yang telah disediakan Al; tidak membuat migration shared baru.
- Membuat UI dan controller/service untuk memilih banyak anggota daftar sebagai assignee ketika create/edit task.
- Memvalidasi semua assignee adalah anggota daftar, mencegah duplicate, dan menyinkronkan penambahan/penghapusan assignment dalam satu transaction dengan perubahan task.
- Membuat halaman/section **Tugas Saya** yang hanya menampilkan task dengan assignment bagi user login.
- **DoD:** satu task dapat memiliki banyak assignee; non-member tidak dapat dipilih; kegagalan satu assignee membatalkan seluruh perubahan; daftar tugas saya benar dan tidak membocorkan task lain.

## Urutan Integrasi

1. Al mengerjakan dan merge `feature/foundation-lists` terlebih dahulu.
2. Agil dan Galang membuat branch dari `main` setelah foundation merge; keduanya independen dan dapat berjalan paralel.
3. Setelah task CRUD Galang ter-merge, Abhi membuat branch dari `main` terbaru dan mengintegrasikan multi-assignee tanpa mengubah kontrak task Galang.
4. Urutan merge yang disarankan: foundation → auth-admin dan tasks (bebas urutan) → task-assignees → regression test bersama.

Ketergantungan Abhi pada Galang tidak dapat dibuat sepenuhnya independen karena assignment adalah relasi dari task dan form create/edit task. Pemisahan controller/service serta hook pada form task menjaga konflik tetap kecil.

## File Ownership Awal

| Owner | File/folder utama |
|---|---|
| Al | migrations foundation, `app/Models`, policy, middleware, `List/ProjectController`, `resources/views/lists` atau `projects`, layout, route skeleton. |
| Agil | `AuthController`, `AdminUserController`, `resources/views/auth`, `resources/views/admin`, auth/admin tests. |
| Galang | `TaskController`, request validation task, `resources/views/tasks`, task/progress tests. |
| Abhi | `TaskAssignmentController` atau service, `resources/views/tasks/_assignees`, `resources/views/tasks/mine`, assignment tests. |

## Checklist sebelum Merge

- [ ] Tidak ada perubahan di luar scope tanpa persetujuan owner file.
- [ ] Validation dan authorization diuji untuk jalur sukses serta gagal.
- [ ] Proses multi-row memakai transaction dan memiliki test rollback.
- [ ] Query memakai Eloquent/query builder atau prepared statement.
- [ ] Migration, test, dan smoke test dijalankan dari database bersih.