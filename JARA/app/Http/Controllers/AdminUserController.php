<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Menampilkan daftar semua akun pengguna (FR-04).
     */
    public function index(): View
    {
        $this->authorizeAdmin();

        $users = User::orderBy('id', 'asc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir pembuatan akun pengguna baru (FR-05).
     */
    public function create(): View
    {
        $this->authorizeAdmin();

        return view('admin.users.create');
    }

    /**
     * Menyimpan akun baru dengan role 'admin' atau 'user' (FR-05).
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
     * Menghapus permanen akun pengguna selain akun admin itu sendiri (FR-06).
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        // BR-05: Admin tidak boleh menghapus akun yang sedang dipakainya sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Admin tidak dapat menghapus akun miliknya sendiri.');
        }

        // Hard delete row user
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dihapus permanen.');
    }
}
