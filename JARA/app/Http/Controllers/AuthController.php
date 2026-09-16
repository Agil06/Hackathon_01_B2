<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     * FR-01: Guest dapat mendaftarkan akun pengguna baru
     * UC-01: Form pendaftaran akun publik
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    /**
     * Handle registration submission.
     * FR-01: Guest dapat mendaftarkan akun pengguna dengan nama, email, password, dan konfirmasi password.
     * BR-01: Email wajib, berformat email, dan unik. Password minimal 8 karakter dan harus dikonfirmasi. Hash password.
     * BR-02: Registrasi publik selalu membuat role 'user'.
     * BR-10: Validasi server wajib, input invalid tidak mengubah database.
     * AC-01: Akun tersimpan dengan role 'user' dan password hash; redirect ke login.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // BR-02: Registrasi publik selalu role 'user'
        ]);

        // AC-01: Pengguna diarahkan ke login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    /**
     * Show the login form.
     * FR-02: Pengguna terdaftar dapat login menggunakan sesi yang aman
     * UC-01: Form login untuk guest
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Handle login submission.
     * FR-02: Pengguna terdaftar dapat login dengan email dan password.
     * BR-01: Autentikasi berbasis email dan password hash.
     * BR-10: Validasi server untuk format kredensial email & password.
     * BR-13: Session diregenerasi setelah login berhasil untuk keamanan sesi.
     * AC-02: Kredensial valid membuat sesi aktif dan menampilkan dashboard; kredensial salah ditolak tanpa membuat sesi.
     * UC-01: Alur autentikasi dan pembuatan sesi pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // AC-02: Pengguna diarahkan ke dashboard atau intended URL dengan sesi aktif
            return redirect()->intended(route('projects.index'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        // AC-02 & Section 10: Kredensial salah ditolak, tampilkan pesan error dan pertahankan input email
        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout.
     * FR-02: Pengguna terdaftar dapat login dan logout menggunakan sesi yang aman.
     * BR-13: Sesi diinvalidasi dan token CSRF diregenerasi saat logout untuk keamanan sesi.
     * AC-02: Pengguna keluar dari status autentikasi dan diarahkan kembali ke halaman login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }
}
