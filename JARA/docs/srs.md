# Software Requirements Specification JARA

## 1. Tujuan dan Ruang Lingkup

JARA adalah aplikasi web advanced to-do list untuk mengelola tugas pribadi maupun tim. Pengguna membuat **Daftar Tugas** (disebut juga *project* pada nama model dan tabel aplikasi), lalu mengelola tugas di dalamnya. Daftar dapat dibagikan kepada pengguna lain. Satu tugas dapat ditugaskan kepada nol, satu, atau banyak anggota daftar.

Sistem mencakup autentikasi, administrasi akun, daftar tugas, keanggotaan, tugas, penugasan, validasi, otorisasi, dan integritas transaksi. Tidak mencakup notifikasi, komentar, lampiran, tag, invitation/approval, API publik, soft delete, atau audit log.

## 2. Aktor dan Hak Utama

| Aktor | Hak |
|---|---|
| Guest | Registrasi dan login. |
| Pengguna | Membuat daftar; melihat daftar yang dimilikinya atau dibagikan kepadanya; mengelola tugas sesuai hak daftar. |
| Pemilik daftar | Pembuat daftar. Mengubah atau menghapus daftar miliknya serta menambah atau menghapus anggota. |
| Anggota daftar | Mengakses daftar yang dibagikan kepadanya dan membuat, melihat, mengubah, menandai selesai, atau menghapus tugas di daftar tersebut. Tidak dapat mengubah daftar/keanggotaan. |
| Admin | Menambah, melihat, dan menghapus akun pengguna. Admin tidak otomatis menjadi anggota semua daftar. |

## 3. Requirement Fungsional

| ID | Requirement |
|---|---|
| FR-01 | Guest dapat mendaftarkan akun pengguna dengan nama, email, password, dan konfirmasi password. |
| FR-02 | Pengguna terdaftar dapat login dan logout menggunakan sesi yang aman. |
| FR-03 | Admin dapat melihat daftar akun, menambah akun, dan menghapus akun lain; admin tidak boleh menghapus akunnya sendiri. |
| FR-04 | Pengguna dapat membuat daftar tugas baru dan otomatis menjadi pemilik serta anggota daftar tersebut. |
| FR-05 | Pengguna dapat melihat hanya daftar yang ia miliki atau yang memiliki membership untuknya. |
| FR-06 | Pemilik dapat mengubah nama daftar miliknya. |
| FR-07 | Pemilik dapat menghapus daftar miliknya; seluruh tugas, penugasan tugas, dan membership daftar ikut terhapus permanen. |
| FR-08 | Pemilik dapat menambahkan pengguna terdaftar ke daftar berdasarkan email dan dapat menghapus anggota selain dirinya sendiri. |
| FR-09 | Sistem menolak setiap akses daftar, tugas, atau penugasan oleh pengguna yang bukan anggota daftar. |
| FR-10 | Anggota daftar dapat membuat tugas dengan judul, prioritas, dan tenggat waktu opsional. Tugas baru berstatus `not_done`. |
| FR-11 | Anggota daftar dapat melihat detail dan daftar tugas di daftar yang diaksesnya. |
| FR-12 | Anggota daftar dapat mengubah judul, prioritas, tenggat waktu, dan status tugas. Status yang tersedia adalah `not_done`, `in_progress`, dan `done`; aksi menandai selesai mengubah status menjadi `done`. |
| FR-13 | Anggota daftar dapat menghapus tugas. |
| FR-14 | Anggota daftar dapat menetapkan satu tugas kepada nol, satu, atau lebih anggota dari daftar yang sama; penugasan dapat ditambah dan dihapus. |
| FR-15 | Sistem menampilkan assignee setiap tugas dan daftar tugas yang ditugaskan kepada pengguna yang sedang login. |
| FR-16 | Sistem menghitung progres daftar saat dibaca: `Not Started` jika tidak ada tugas atau semua `not_done`; `In Progress` jika ada tugas mulai/done tetapi belum semuanya done; `Completed` jika ada tugas dan semuanya done. |
| FR-17 | Seluruh skema database dapat dibangun ulang melalui migration dan tidak membutuhkan perubahan tabel manual. |

## 4. Aturan Bisnis dan Validasi

| ID | Aturan |
|---|---|
| BR-01 | Email wajib, berformat email, dan unik. Password minimal 8 karakter dan harus dikonfirmasi. Password disimpan sebagai hash. |
| BR-02 | Role akun hanya `admin` atau `user`. Registrasi publik selalu membuat role `user`. |
| BR-03 | Nama daftar dan judul tugas wajib berupa string maksimal 255 karakter. |
| BR-04 | Prioritas hanya `low`, `medium`, atau `high`; default `medium`. Deadline opsional dan, bila diisi, harus tanggal valid. |
| BR-05 | Status tugas hanya `not_done`, `in_progress`, atau `done`; semua perpindahan status diizinkan. |
| BR-06 | Satu daftar mempunyai tepat satu owner (`owner_id`). Owner harus juga memiliki satu membership pada daftar. |
| BR-07 | Pasangan membership `(list_id, user_id)` unik. Pengguna yang sudah menjadi anggota tidak dapat ditambahkan lagi. |
| BR-08 | Hanya owner yang boleh mengubah/menghapus daftar dan menambah/menghapus anggota. Owner tidak dapat dihapus dari membership. |
| BR-09 | Tugas wajib berada dalam tepat satu daftar. Assignee wajib merupakan anggota daftar dari tugas tersebut. Pasangan `(task_id, user_id)` unik. |
| BR-10 | Semua request mutasi wajib melalui validasi server. Input invalid tidak boleh mengubah database. |
| BR-11 | Semua request daftar/tugas/assignment memerlukan autentikasi; pengguna yang login tetapi tidak berwenang menerima HTTP 403. Resource tidak ada menerima HTTP 404. |
| BR-12 | Semua query database harus memakai Eloquent/query builder Laravel atau prepared statement berparameter; SQL yang menggabungkan input pengguna secara string dilarang. |
| BR-13 | CSRF protection digunakan untuk seluruh form mutasi; session diregenerasi setelah login dan diinvalidate saat logout. |
| BR-14 | Penghapusan adalah hard delete. Foreign key cascade wajib menghapus data turunan yang relevan. |
| BR-15 | Saat admin menghapus akun, membership dan assignment akun tersebut ikut dihapus. Jika akun adalah owner daftar, daftar miliknya beserta seluruh data turunannya ikut terhapus agar tidak ada daftar tanpa owner. |

## 5. Kebutuhan Atomisitas

Setiap proses berikut dijalankan dalam database transaction. Bila satu langkah gagal, seluruh perubahan dibatalkan dan tidak ada data setengah jadi.

| Proses | Unit atomik |
|---|---|
| Membuat daftar | Buat `lists` lalu buat membership owner. |
| Menambah anggota | Verifikasi owner, cari akun, cek duplicate, lalu buat membership. |
| Menghapus anggota | Verifikasi owner, pastikan target bukan owner, hapus seluruh assignment target pada task dalam daftar, lalu hapus membership. |
| Membuat/mengubah tugas beserta assignee | Validasi tugas, verifikasi semua assignee adalah member, simpan task, lalu sinkronkan semua assignment. |
| Menghapus daftar | Hapus daftar; FK cascade membersihkan tasks, task assignments, dan memberships. |
| Menghapus akun | Verifikasi admin dan bukan self-delete; hapus akun beserta relasi sesuai FK, termasuk daftar miliknya bila akun adalah owner. |

Implementasi proses tersebut menggunakan `DB::transaction(...)`. Constraint FK dan unique index tetap diperlukan sebagai perlindungan terakhir terhadap race condition atau bypass aplikasi.

## 6. Model Data Minimum

| Entitas | Field penting | Relasi |
|---|---|---|
| `users` | id, name, email unik, password hash, role | memiliki daftar, membership, dan assignment. |
| `lists` | id, owner_id, name | satu owner, banyak membership, banyak task. Nama tabel boleh tetap `projects` bila baseline kode memakai istilah tersebut. |
| `list_user` | list_id, user_id | membership M:N yang unik. |
| `tasks` | id, list_id, title, priority, deadline nullable, status | milik satu daftar dan memiliki banyak assignee. |
| `task_user` | task_id, user_id | assignment M:N yang unik; user harus anggota daftar induk. |

Foreign key minimum: `lists.owner_id -> users`, `list_user.list_id -> lists`, `list_user.user_id -> users`, `tasks.list_id -> lists`, serta `task_user.task_id -> tasks` dan `task_user.user_id -> users`. Hapus daftar meng-cascade tasks, memberships, dan assignments; hapus task meng-cascade assignments.

## 7. Matriks Otorisasi

| Aksi | Guest | Non-member | Anggota | Owner | Admin non-member |
|---|---:|---:|---:|---:|---:|
| Membuat daftar | Tidak | Ya | Ya | Ya | Ya |
| Melihat daftar/tugas | Tidak | Tidak | Ya | Ya | Tidak |
| Membuat, ubah, hapus tugas | Tidak | Tidak | Ya | Ya | Tidak |
| Mengatur assignee | Tidak | Tidak | Ya | Ya | Tidak |
| Ubah/hapus daftar | Tidak | Tidak | Tidak | Ya | Tidak |
| Tambah/hapus anggota | Tidak | Tidak | Tidak | Ya | Tidak |
| Kelola akun | Tidak | Tidak | Tidak | Tidak | Ya |

## 8. Acceptance Criteria

| ID | Given When Then |
|---|---|
| AC-01 | Given guest, when registrasi valid, then akun `user` tersimpan dengan password hash; email duplikat atau input invalid ditolak tanpa akun baru. |
| AC-02 | Given credential valid, when login, then sesi aktif dan dashboard daftar tampil; credential salah tidak membuat sesi. |
| AC-03 | Given admin, when mengelola akun valid, then akun dibuat/dihapus; non-admin dan self-delete ditolak. |
| AC-04 | Given user membuat daftar valid, then owner dan membership owner tercipta bersama-sama. Jika pembuatan membership gagal, daftar juga tidak tersimpan. |
| AC-05 | Given member membuka dashboard, then hanya daftar miliknya atau daftar bersama yang tampil. |
| AC-06 | Given owner menambah user terdaftar, then membership baru tersimpan; email tidak ditemukan/duplicate dan request non-owner tidak mengubah data. |
| AC-07 | Given owner menghapus daftar, then daftar, seluruh tasks, task assignments, dan memberships tidak lagi ada. |
| AC-08 | Given member membuat/mengubah task valid, then perubahan tersimpan; nilai priority/status/deadline invalid ditolak tanpa perubahan. |
| AC-09 | Given member menetapkan beberapa anggota daftar pada task, then setiap assignment tersimpan dan terlihat; assignee non-member ditolak serta tidak ada assignment parsial. |
| AC-10 | Given non-member mencoba URL daftar, tugas, atau assignment, then response 403 dan database tidak berubah. |
| AC-11 | Given task ditandai selesai, then statusnya `done` dan progres daftar diperbarui saat halaman dibaca. |
| AC-12 | Given database kosong, when migration dan seed dijalankan, then seluruh tabel, FK, unique constraint, dan admin development dibuat tanpa schema manual. |

## 9. Use Case Utama

### UC-01 Registrasi dan Login

Guest mengisi form registrasi. Sistem memvalidasi input, membuat akun dengan role `user`, lalu pengguna login menggunakan email dan password. Login yang valid membuat sesi baru dan menampilkan dashboard. Email duplikat, konfirmasi password salah, atau kredensial salah hanya menampilkan error dan tidak membuat data/sesi yang tidak valid.

### UC-02 Membuat dan Membagikan Daftar

Pengguna login membuat daftar. Sistem dalam satu transaction menyimpan daftar dan membership owner. Owner membuka detail daftar lalu menambah pengguna terdaftar berdasarkan email. Member baru langsung dapat membuka daftar dan mengelola tugas, tetapi tidak dapat mengubah daftar atau membership. Hanya owner dapat menghapus member; assignment member itu pada daftar yang sama harus ikut dibersihkan dalam transaction.

### UC-03 Mengelola Tugas dan Assignee

Anggota daftar membuat atau mengubah tugas. Sistem memvalidasi field task serta seluruh ID assignee, memastikan semua assignee anggota daftar, kemudian menyimpan task dan sinkronisasi assignment sebagai satu unit atomik. Jika satu assignee tidak valid, task dan semua assignment tetap seperti sebelum request. Anggota dapat mengubah status hingga `done` atau menghapus task.

### UC-04 Administrasi Akun

Admin login membuka area akun untuk membuat atau menghapus akun lain. Sistem menolak user biasa dan self-delete. Menghapus akun owner menghapus daftar miliknya melalui cascade agar tidak ada daftar tanpa owner; membership dan assignment akun pada daftar lain ikut hilang.

## 10. Perilaku Error

- Validasi gagal: kembali ke form, tampilkan error per-field dan pertahankan old input selain password; database tidak berubah.
- Guest ke resource terlindungi: redirect ke login. Pengguna login yang tidak memenuhi policy: HTTP 403. ID resource yang tidak ada atau task yang bukan milik daftar URL: HTTP 404.
- Duplicate email, membership, dan assignment: tampilkan pesan yang dapat dipahami; unique constraint tetap melindungi database.
- Kesalahan database pada proses atomik: transaction rollback, tampilkan error umum yang aman, dan log detail teknis di server tanpa menampilkan query/input sensitif kepada pengguna.

## 11. Nonfunctional dan Keamanan

- Aplikasi menggunakan Laravel MVC, Blade, MySQL, Eloquent, validation server-side, policy/middleware, dan migration.
- Semua operasi tulis memakai POST/PUT/PATCH/DELETE dengan CSRF token, bukan GET.
- Otorisasi dipusatkan pada `ListPolicy`/`ProjectPolicy` dan diperiksa lagi pada route/controller untuk task serta assignee.
- Pesan validasi dapat dipahami pengguna dan old input dipertahankan kecuali password.
- Tidak ada SQL mentah dengan interpolasi input pengguna. Bila SQL mentah diperlukan, gunakan binding parameter.
- Pengujian minimal mencakup otorisasi, validasi, transaksi rollback, FK/unique constraint, dan assignment multi-user.

## 12. Asumsi yang Dikunci

- Email menjadi identitas login serta pencarian anggota.
- Anggota dapat mengelola tugas dan assignee, sedangkan hanya owner mengelola daftar/anggota.
- Tugas boleh belum mempunyai assignee agar mendukung tugas pribadi atau backlog.
- Admin boleh membuat daftar untuk dirinya sendiri, tetapi tidak memperoleh bypass pada daftar milik pengguna lain.
- Deadline disimpan sebagai tanggal tanpa jam; tanggal lampau tetap valid karena user story tidak melarangnya.
