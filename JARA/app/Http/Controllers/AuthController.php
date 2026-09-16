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
            return redirect()->route('projects.index');
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
     * FR-02: Pengguna terdaftar dapat login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('projects.index');
        }
        return view('auth.login');
    }

    /**
     * Handle login submission.
     * FR-02: Login dengan email dan password yang valid
     * BR: Session diregenerasi saat login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // AC-03: User diarahkan ke daftar project
            return redirect()->intended(route('projects.index'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        // AC-03: Credential salah tetap di login dengan error
        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout.
     * FR-03: Pengguna terautentikasi dapat logout
     * BR: Session diregenerasi saat logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // AC-04: User diarahkan ke login
        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }
}