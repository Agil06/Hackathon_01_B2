# Software Requirements Specification (SRS) — JARA

## 1. Ringkasan Sistem

JARA adalah aplikasi web server-rendered berbasis Laravel dan MySQL untuk mengelola project dan task pribadi maupun kolaboratif. Pengguna terautentikasi hanya dapat mengakses project tempat ia menjadi anggota. Creator dan collaborator memiliki hak yang sama di dalam project. Admin Sistem mengelola akun pengguna. Seluruh penghapusan bersifat permanen dan seluruh schema harus dapat direproduksi melalui migration.

Baseline implementasi: Laravel MVC, Blade, session authentication, Eloquent, server-side validation, dan MySQL lokal. REST API, SPA, invitation workflow, soft delete, notification, dan role per-project tidak termasuk scope.

## 2. Klasifikasi Requirement

### 2.1 Confirmed requirement

- Registrasi dan login pengguna reguler.
- Admin dapat membuat dan menghapus akun.
- Anggota project dapat CRUD project/task, menambah collaborator, mengubah priority/status/deadline, dan melihat progress.
- Creator dan collaborator mempunyai permission project yang sama.
- Data project dibatasi berdasarkan membership.
- Penghapusan akun, project, dan task bersifat permanen.
- Database direproduksi dengan migration; seed/factory hanya bila diperlukan.

### 2.2 Technical necessity yang dikunci

- Password disimpan sebagai hash dan session diregenerasi saat login/logout.
- CSRF protection digunakan pada semua form mutasi.
- Creator juga dicatat sebagai anggota pada pivot `project_user`; `creator_id` tetap menyimpan asal creator.
- Progress project dihitung dari task saat dibaca dan tidak disimpan sebagai kolom.
- Authorization dipusatkan pada `ProjectPolicy`; akses task diperiksa melalui project induknya.
- Nilai enum disimpan sebagai snake_case: priority `low|medium|high`, status `not_done|in_progress|done`; UI menampilkan label sesuai user story.

### 2.3 Implementation assumptions

- Identitas login dan target collaborator adalah `email`; `name` adalah nama tampilan. Tidak dibuat field `username` terpisah.
- Akun hanya mempunyai role global `admin` atau `user`. Admin tidak otomatis mendapat akses ke semua project.
- Saat creator dihapus, project miliknya beserta task dan membership ikut dihapus (`ON DELETE CASCADE`). Membership user tersebut pada project lain ikut dihapus.
- Admin awal dibuat oleh `AdminUserSeeder` menggunakan nilai development yang didokumentasikan; credential harus diganti untuk penggunaan di luar praktikum.
- Deadline memakai tanggal (`date`), bukan waktu, dan boleh kosong.
- Nama project dan judul task maksimal 255 karakter; deskripsi project/task tidak ditambahkan karena tidak disebutkan.

### 2.4 Blocking clarification untuk PM sebelum coding

`BC-01`: Konfirmasi apakah penghapusan creator memang harus menghapus seluruh project yang dibuatnya. Rancangan awal mengunci perilaku cascade agar tidak ada project tanpa creator. Jika jawaban berbeda, schema, policy, dan acceptance test harus direvisi sebelum branch dibuat.

`BC-02`: Konfirmasi apakah “username atau identitas akun” berarti perlu username terpisah. Rancangan awal memakai email unik sebagai identitas login; menambah username akan mengubah form, schema, dan test.

## 3. Actors

- **Pengguna Umum (Guest):** hanya membuka halaman register/login dan mengirim form authentication.
- **Pengguna Reguler:** memakai fitur project/task pada project tempat ia menjadi anggota.
- **Admin Sistem:** membuat dan menghapus akun melalui area admin; bukan anggota otomatis dari project pengguna.

## 4. Functional Requirements

| ID | Requirement atomik |
|---|---|
| FR-01 | Guest dapat membuka dan mengirim form registrasi akun reguler. |
| FR-02 | Pengguna terdaftar dapat login dengan email dan password yang valid. |
| FR-03 | Pengguna terautentikasi dapat logout. |
| FR-04 | Admin dapat melihat daftar akun pengguna. |
| FR-05 | Admin dapat membuat akun `admin` atau `user`. |
| FR-06 | Admin dapat menghapus permanen akun selain akunnya sendiri. |
| FR-07 | Pengguna reguler dapat melihat daftar project tempat ia menjadi anggota. |
| FR-08 | Pengguna reguler dapat membuat project dan otomatis menjadi anggotanya. |
| FR-09 | Anggota project dapat melihat detail project beserta task, collaborator, dan progress. |
| FR-10 | Anggota project dapat mengubah nama project. |
| FR-11 | Anggota project dapat menghapus permanen project. |
| FR-12 | Anggota project dapat menambahkan akun terdaftar sebagai collaborator berdasarkan email. |
| FR-13 | Sistem menolak akses melihat atau memanipulasi project bagi non-anggota. |
| FR-14 | Anggota project dapat membuat task di dalam project. |
| FR-15 | Anggota project dapat melihat detail task di project. |
| FR-16 | Anggota project dapat mengubah title, priority, deadline, dan status task. |
| FR-17 | Anggota project dapat menghapus permanen task. |
| FR-18 | Task baru memiliki status default `Not Done`. |
| FR-19 | Priority task dibatasi pada `Low`, `Medium`, atau `High`. |
| FR-20 | Status task dibatasi pada `Not Done`, `In Progress`, atau `Done`, dan boleh berpindah antarnilai tanpa approval. |
| FR-21 | Sistem menampilkan progress `Not Started` untuk project tanpa task atau seluruh task `Not Done`. |
| FR-22 | Sistem menampilkan progress `In Progress` bila sedikitnya satu task `In Progress`/`Done` dan belum seluruh task `Done`. |
| FR-23 | Sistem menampilkan progress `Completed` bila project memiliki task dan seluruh task `Done`. |
| FR-24 | Penghapusan project menghapus permanen seluruh task dan membership project. |
| FR-25 | Schema aplikasi dapat dibuat ulang pada MySQL dengan Laravel migration. |

## 5. Business Rules

| ID | Aturan |
|---|---|
| BR-01 | Email wajib, format valid, dan unik di `users`; setelah row dihapus permanen, email dapat dipakai lagi. |
| BR-02 | Password minimal 8 karakter dan harus dikonfirmasi saat registrasi/pembuatan akun. |
| BR-03 | Hanya role global `admin` dan `user` yang valid. Registrasi publik selalu menghasilkan role `user`. |
| BR-04 | Hanya admin boleh membuka dan menjalankan operasi `/admin/users`. |
| BR-05 | Admin tidak boleh menghapus akun yang sedang dipakainya sendiri. |
| BR-06 | Nama project dan title task wajib, string, maksimum 255 karakter. |
| BR-07 | Setiap project mempunyai tepat satu `creator_id`; creator juga mempunyai satu row membership. |
| BR-08 | Membership `(project_id,user_id)` unik; menambah anggota yang sudah ada ditolak tanpa membuat duplikasi. |
| BR-09 | Target collaborator harus ditemukan berdasarkan email akun terdaftar. Tidak ada invitation/approval. |
| BR-10 | Semua aksi project/task/add-collaborator membutuhkan authentication dan membership project. |
| BR-11 | Setiap task wajib terkait tepat satu project. |
| BR-12 | Priority wajib salah satu `low`, `medium`, `high`. |
| BR-13 | Deadline opsional dan, bila diisi, harus berupa tanggal valid. Tidak ada larangan tanggal lampau karena tidak dinyatakan. |
| BR-14 | Status wajib salah satu `not_done`, `in_progress`, `done`; semua perpindahan antarnilai diizinkan. |
| BR-15 | Task baru selalu `not_done`; form create tidak menerima status. |
| BR-16 | Progress tidak disimpan: 0 task atau semua `not_done` = `Not Started`; semua `done` dan jumlah task > 0 = `Completed`; selain itu = `In Progress`. |
| BR-17 | Delete memakai hard delete. FK cascade membersihkan data turunan sesuai ERD. |

## 6. Acceptance Criteria

| ID | Given / When / Then |
|---|---|
| AC-01 | Given guest berada di register, when data valid dikirim, then akun role `user` tersimpan dan pengguna diarahkan ke login. |
| AC-02 | Given email sudah terdaftar atau konfirmasi password berbeda, when registrasi dikirim, then form ditolak, pesan validasi tampil, dan tidak ada akun baru. |
| AC-03 | Given credential valid, when login, then session terautentikasi dan user diarahkan ke daftar project; credential salah tetap di login dengan error. |
| AC-04 | Given user login, when logout, then session authentication berakhir dan user diarahkan ke login. |
| AC-05 | Given admin login, when membuka user list, then semua akun tampil; regular user mendapat 403. |
| AC-06 | Given admin mengirim create-user valid, then akun dengan role pilihan tersimpan; email duplikat ditolak. |
| AC-07 | Given admin menghapus akun lain, then row akun hilang permanen dan email dapat didaftarkan lagi; self-delete ditolak. |
| AC-08 | Given regular user membuat project bernama valid, then project tersimpan dengan `creator_id` dirinya dan membership creator tercipta. |
| AC-09 | Given user adalah member, when membuka dashboard/detail, then project, tasks, members, dan progress yang benar tampil. |
| AC-10 | Given user bukan member, when mencoba URL show/edit/update/delete project atau task, then response 403 dan data tidak berubah. |
| AC-11 | Given member memperbarui nama project valid, then nama tersimpan dan detail project tampil; nama kosong ditolak. |
| AC-12 | Given member menghapus project, then project, seluruh task, dan seluruh pivot membership terkait tidak lagi ada. |
| AC-13 | Given member memasukkan email akun terdaftar yang belum menjadi member, then membership langsung tersimpan; email tidak ditemukan atau duplicate menampilkan error. |
| AC-14 | Given member mengirim task valid, then task terkait project tersimpan dengan status `not_done`. |
| AC-15 | Given member mengubah seluruh field task dengan nilai valid, then perubahan persisted dan detail project tampil. |
| AC-16 | Given priority/status di luar daftar atau deadline bukan tanggal, when form dikirim, then request ditolak dan database tidak berubah. |
| AC-17 | Given member menghapus task, then row task hilang permanen. |
| AC-18 | Given project tanpa task atau seluruh task `not_done`, then progress adalah `Not Started`. |
| AC-19 | Given sebagian pekerjaan telah dimulai/selesai tetapi tidak semua task `done`, then progress adalah `In Progress`. |
| AC-20 | Given project memiliki task dan semuanya `done`, then progress adalah `Completed`; mengubah satu task kembali mengubah progress sesuai aturan. |
| AC-21 | Given database kosong dan `.env` MySQL valid, when `php artisan migrate --seed`, then seluruh tabel dan data admin development dibuat tanpa langkah schema manual. |

## 7. Textual Use Cases

### UC-01 — Authentication

- **Actor:** Guest / pengguna terdaftar.
- **Precondition:** Guest belum login.
- **Trigger:** membuka register atau login.
- **Main flow:** guest registrasi → data divalidasi → akun `user` dibuat → login dengan credential → session dibuat → diarahkan ke project list.
- **Alternative/error:** email duplicate, password confirmation salah, atau credential salah menampilkan error dan tidak membuat session/data tak valid.
- **Postcondition:** user terautentikasi atau tetap guest.

### UC-02 — Membuat dan berkolaborasi dalam project

- **Actor:** Pengguna reguler terautentikasi.
- **Precondition:** akun aktif.
- **Trigger:** user membuat project lalu menambah collaborator.
- **Main flow:** isi nama project → project dan membership creator tersimpan → buka detail → isi email collaborator → akun ditemukan → membership langsung tersimpan.
- **Alternative/error:** nama invalid, email tidak ditemukan, atau sudah menjadi member menghasilkan validation error.
- **Postcondition:** semua member dapat melakukan operasi project/task yang sama.

### UC-03 — Mengelola task dan progress

- **Actor:** Anggota project.
- **Precondition:** project ada dan actor merupakan member.
- **Trigger:** actor membuat/mengubah/menghapus task.
- **Main flow:** submit task → validasi → simpan → redirect ke detail project → progress dihitung ulang dari status semua task.
- **Alternative/error:** input invalid ditolak; non-member memperoleh 403; resource tidak ada memperoleh 404.
- **Postcondition:** task persisted sesuai aksi dan progress terbaru tampil.

### UC-04 — Administrasi akun

- **Actor:** Admin Sistem.
- **Precondition:** login dengan role `admin`.
- **Trigger:** membuka area user management.
- **Main flow:** melihat daftar → membuat akun atau memilih delete akun lain → perubahan persisted.
- **Alternative/error:** non-admin 403; duplicate email ditolak; self-delete ditolak.
- **Postcondition:** daftar akun konsisten; hard delete memungkinkan reuse email.

## 8. Data Requirements

- **User:** name, email unik, password hash, global role, timestamps.
- **Project:** nama, creator reference, timestamps.
- **Project membership:** pasangan project-user unik dan timestamps; tidak menyimpan role/invitation state.
- **Task:** project reference, title, priority kategorikal, deadline opsional, completion status, timestamps.
- Relationship: User 1—N created Project; User M—N Project melalui membership; Project 1—N Task.
- Progress adalah derived data dan tidak disimpan.

## 9. Validation and Error Behaviour

- Validation gagal: redirect kembali, old input dipertahankan, pesan field-level ditampilkan, database tidak berubah.
- Authentication gagal: login tetap tampil dengan pesan credential tidak valid tanpa membocorkan apakah email ada.
- Guest ke halaman terlindungi: redirect ke login.
- Authenticated tetapi tidak berhak: HTTP 403.
- Model/path ID tidak ditemukan: HTTP 404 melalui route-model binding.
- Duplicate email/membership: validation error yang mudah dipahami; unique constraint tetap menjadi perlindungan terakhir.
- Delete self oleh admin: redirect kembali dengan error; tidak ada perubahan.
- Operasi multi-row create project + creator membership menggunakan transaction agar tidak menghasilkan data setengah jadi.

## 10. Learning Scope Mapping

| Konsep | Requirement | Realisasi |
|---|---|---|
| Routing & HTTP | FR-01–FR-24 | Named web routes, GET/POST/PATCH/DELETE. |
| Controller processing | FR-01–FR-24 | Authentication, admin, project, collaborator, dan task controllers. |
| Form submission & validation | FR-01, FR-02, FR-05, FR-08, FR-10, FR-12, FR-14, FR-16 | Laravel server-side validation dan error bag. |
| Session | FR-02, FR-03 | `Auth::attempt`, regenerate, logout, invalidate. |
| Database/Eloquent | FR-04–FR-25 | Models, relations, migration, transaction, cascade. |
| Blade rendering | FR-01–FR-23 | Forms, lists, detail, status/progress, error/empty state. |
| Authorization | FR-04–FR-06, FR-13 | Role middleware dan `ProjectPolicy`. |

## 11. Assumptions and Open Questions

- **Confirmed:** nilai status/priority, equality creator/collaborator, no invitation, hard delete, membership-only access, MySQL/migrations.
- **Technical necessity:** password hash, CSRF, policy, creator membership pivot, computed progress.
- **Assumption:** email sebagai login/collaborator identity; admin tidak otomatis boleh melihat project; deadline date-only; cascade project saat creator dihapus.
- **Open/blocking:** BC-01 dan BC-02 harus diputuskan PM. Seluruh dokumen memakai provisional decision di atas agar implementasi tetap dapat dimulai setelah PM menyetujuinya.

## 12. Implementation Risk

- Scope authentication + admin + project + collaboration + task cukup padat untuk 2 jam. Mitigasi: gunakan Blade sederhana, tanpa CSS framework wajib, tanpa API, tanpa service/repository, dan ikuti branch/contract di `design.md`.
- Cascade account deletion berdampak luas. PM wajib mengunci BC-01 sebelum migration foundation dibuat.
- Shared route/layout/model rawan conflict. Satu owner foundation membuat skeleton; branch lain mengisi file feature-specific dan perubahan shared diajukan melalui PM.
