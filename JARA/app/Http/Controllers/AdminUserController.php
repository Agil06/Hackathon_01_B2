<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Memastikan hanya user dengan role 'admin' yang dapat mengakses operasi admin.
     */
    protected function authorizeAdmin(): void
    {
        if (! auth()->check()) {
            abort(401);
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang diperbolehkan mengakses halaman ini.');
        }
    }

    /**
     * Menampilkan daftar semua akun pengguna (FR-03, UC-04).
     * BR-02: Akun memiliki role 'admin' atau 'user'.
     * BR-11: Akses dibatasi khusus untuk admin terautentikasi; user biasa menerima HTTP 403.
     * BR-12: Query database menggunakan Eloquent berparameter.
     * AC-03: Admin dapat melihat seluruh akun pengguna yang terdaftar.
     */
    public function index(): View
    {
        $this->authorizeAdmin();

        $users = User::orderBy('id', 'asc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir pembuatan akun pengguna baru (FR-03, UC-04).
     */
    public function create(): View
    {
        $this->authorizeAdmin();

        return view('admin.users.create');
    }

    /**
     * Menyimpan akun baru dengan role 'admin' atau 'user' (FR-03, UC-04).
     * BR-01: Email unik, valid, dan password minimal 8 karakter terkonfirmasi serta di-hash.
     * BR-02: Role yang diizinkan hanya 'admin' atau 'user'.
     * BR-10: Validasi server ketat, input tidak valid ditolak tanpa mengubah database.
     * AC-03: Akun pengguna berhasil dibuat oleh admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,user'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dibuat.');
    }

    /**
     * Menghapus permanen akun pengguna selain akun admin itu sendiri (FR-03, UC-04).
     * FR-03: Admin dapat menghapus akun lain; admin tidak boleh menghapus akunnya sendiri.
     * BR-14: Hard delete permanen dengan cascade ke relasi data turunan.
     * BR-15: Penghapusan akun menghapus membership, assignment, dan project miliknya jika owner.
     * Section 5: Proses atomik menggunakan DB::transaction().
     * Section 10: Rollback transaksi saat kesalahan database dan log detail teknis di server.
     * AC-03: Akun pengguna berhasil dihapus permanen; self-delete ditolak.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        // FR-03 & AC-03: Admin tidak dapat menghapus akun miliknya sendiri (self-delete ditolak)
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Admin tidak dapat menghapus akun miliknya sendiri.');
        }

        try {
            DB::transaction(function () use ($user) {
                // Hard delete row user (seluruh data turunan cascade terhapus via DB FK constraint)
                $user->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('admin.users.index')
                ->with('error', 'Terjadi kesalahan sistem saat menghapus akun pengguna.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dihapus permanen.');
    }
}
