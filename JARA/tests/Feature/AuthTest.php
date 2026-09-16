<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

test('guest can view registration form (FR-01)', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('Buat Akun');
});

test('guest can register as regular user with role user (FR-01, BR-03, AC-01)', function () {
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
        'role' => 'user', // BR-03: Registrasi publik selalu role user
    ]);

    $user = User::where('email', 'johndoe@example.com')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('registration validation enforces required name, unique email, and confirmed min 8 password (BR-01, BR-02, AC-02)', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    // Empty fields
    $this->post(route('register'), [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ])->assertSessionHasErrors(['name', 'email', 'password']);

    // Duplicate email
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

    // Password confirmation tidak cocok
    $this->post(route('register'), [
        'name' => 'Mismatch Pass User',
        'email' => 'mismatch@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different456',
    ])->assertSessionHasErrors(['password']);
});

test('guest can view login form (FR-02)', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('Login');
});

test('registered user can login with valid credentials (FR-02, AC-03)', function () {
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
    $this->assertAuthenticatedAs($user);
});

test('login rejects invalid credentials and retains only email input (AC-03)', function () {
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
