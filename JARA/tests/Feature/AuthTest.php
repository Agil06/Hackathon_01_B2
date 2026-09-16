<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

test('guest can view registration form (FR-01, UC-01)', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('Buat Akun');
});

test('guest can register as regular user with role user and hashed password (FR-01, BR-01, BR-02, AC-01)', function () {
    $response = $this->post(route('register'), [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'role' => 'user', // BR-02: Registrasi publik selalu role user
    ]);

    $user = User::where('email', 'johndoe@example.com')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('registration validation enforces required fields, valid email, uniqueness, and confirmed min 8 password without persisting invalid data (FR-01, BR-01, BR-10, AC-01)', function () {
    User::factory()->create(['email' => 'existing@example.com']);
    $initialCount = User::count();

    // Field kosong
    $this->post(route('register'), [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ])->assertSessionHasErrors(['name', 'email', 'password']);

    // Format email tidak valid
    $this->post(route('register'), [
        'name' => 'Invalid Email User',
        'email' => 'not-an-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors(['email']);

    // Email duplikat
    $this->post(route('register'), [
        'name' => 'Duplicate User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors(['email']);

    // Password kurang dari 8 karakter
    $this->post(route('register'), [
        'name' => 'Short Pass User',
        'email' => 'short@example.com',
        'password' => 'pass12',
        'password_confirmation' => 'pass12',
    ])->assertSessionHasErrors(['password']);

    // Konfirmasi password tidak cocok
    $this->post(route('register'), [
        'name' => 'Mismatch Pass User',
        'email' => 'mismatch@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different456',
    ])->assertSessionHasErrors(['password']);

    // BR-10 & AC-01: Tidak ada penambahan user pada database saat input invalid
    expect(User::count())->toBe($initialCount);
    $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    $this->assertDatabaseMissing('users', ['email' => 'short@example.com']);
});

test('registration failure preserves old input for name and email but excludes password fields (Section 10)', function () {
    $response = $this->post(route('register'), [
        'name' => 'Preserved Name',
        'email' => 'preserved@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
    expect(session()->getOldInput('name'))->toBe('Preserved Name')
        ->and(session()->getOldInput('email'))->toBe('preserved@example.com')
        ->and(session()->hasOldInput('password'))->toBeFalse()
        ->and(session()->hasOldInput('password_confirmation'))->toBeFalse();
});

test('guest can view login form (FR-02, UC-01)', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('Login');
});

test('login validation enforces required email, valid format, and required password (FR-02, BR-10)', function () {
    // Empty submission
    $this->post(route('login'), [
        'email' => '',
        'password' => '',
    ])->assertSessionHasErrors(['email', 'password']);

    // Invalid email format
    $this->post(route('login'), [
        'email' => 'invalid-email-format',
        'password' => 'somepassword',
    ])->assertSessionHasErrors(['email']);

    $this->assertGuest();
});

test('registered user can login with valid credentials, regenerate session, and access dashboard (FR-02, BR-13, AC-02)', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('secretpassword'),
        'role' => 'user',
    ]);

    $response = $this->post(route('login'), [
        'email' => 'user@example.com',
        'password' => 'secretpassword',
    ]);

    $response->assertRedirect(route('projects.index'));
    $response->assertSessionHas('success');
    $this->assertAuthenticatedAs($user);
});

test('login rejects unregistered email and retains only email in old input (FR-02, AC-02, Section 10)', function () {
    $response = $this->post(route('login'), [
        'email' => 'unknown@example.com',
        'password' => 'secretpassword',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();

    expect(session()->getOldInput('email'))->toBe('unknown@example.com')
        ->and(session()->hasOldInput('password'))->toBeFalse();
});

test('login rejects wrong password for existing user and retains only email in old input (FR-02, AC-02, Section 10)', function () {
    User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('secretpassword'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'user@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();

    expect(session()->getOldInput('email'))->toBe('user@example.com')
        ->and(session()->hasOldInput('password'))->toBeFalse();
});

test('registered user can login with remember me enabled (FR-02)', function () {
    $user = User::factory()->create([
        'email' => 'remember@example.com',
        'password' => Hash::make('secretpassword'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'remember@example.com',
        'password' => 'secretpassword',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('projects.index'));
    $this->assertAuthenticatedAs($user);
});

test('login redirects authenticated user to intended URL (FR-02, AC-02)', function () {
    $user = User::factory()->create([
        'email' => 'intended@example.com',
        'password' => Hash::make('secretpassword'),
    ]);

    // Guest mencoba mengakses halaman project, diarahkan ke login dengan intended URL
    $this->get(route('projects.index'))->assertRedirect(route('login'));

    // Setelah login, user harus diarahkan ke intended URL (projects.index)
    $response = $this->post(route('login'), [
        'email' => 'intended@example.com',
        'password' => 'secretpassword',
    ]);

    $response->assertRedirect(route('projects.index'));
    $this->assertAuthenticatedAs($user);
});

test('authenticated user can logout (FR-03, AC-04)', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('already authenticated user is redirected when visiting login or register', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get(route('login'))->assertRedirect(route('home'));
    $this->actingAs($user)->get(route('register'))->assertRedirect(route('home'));
});
