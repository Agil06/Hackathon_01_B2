<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@jara.test',
            'role' => 'admin',
        ]);

        $this->regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@jara.test',
            'role' => 'user',
        ]);
    }

    /**
     * Tamu (guest) yang belum login tidak dapat mengakses halaman admin user.
     */
    public function test_guest_cannot_access_admin_user_management(): void
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login', [], false) ? route('login') : '/login');

        $response = $this->get(route('admin.users.create'));
        $response->assertRedirect(route('login', [], false) ? route('login') : '/login');
    }

    /**
     * FR-03 & BR-02 & BR-11 & AC-03: Pengguna reguler (non-admin) ditolak dengan HTTP 403 dari area admin.
     */
    public function test_regular_user_is_forbidden_from_admin_user_management(): void
    {
        // Akses daftar user
        $response = $this->actingAs($this->regularUser)->get(route('admin.users.index'));
        $response->assertForbidden();

        // Akses form create user
        $response = $this->actingAs($this->regularUser)->get(route('admin.users.create'));
        $response->assertForbidden();

        // Mencoba simpan user baru
        $response = $this->actingAs($this->regularUser)->post(route('admin.users.store'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);
        $response->assertForbidden();

        // Mencoba hapus user lain
        $targetUser = User::factory()->create();
        $response = $this->actingAs($this->regularUser)->delete(route('admin.users.destroy', $targetUser));
        $response->assertForbidden();
    }

    /**
     * FR-03 & AC-03 & UC-04: Admin dapat melihat daftar seluruh akun pengguna dengan role dan detailnya.
     */
    public function test_admin_can_view_user_list(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');

        $users = $response->viewData('users');
        $this->assertCount(2, $users);
        $this->assertEquals($this->adminUser->id, $users->first()->id);

        $response->assertSeeText($this->adminUser->name);
        $response->assertSeeText($this->adminUser->email);
        $response->assertSeeText($this->regularUser->name);
        $response->assertSeeText($this->regularUser->email);
        $response->assertSeeText('(Anda)');
    }

    /**
     * FR-03 & UC-04: Admin dapat membuka halaman formulir penambahan pengguna.
     */
    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.users.create'));

        $response->assertOk();
        $response->assertViewIs('admin.users.create');
    }

    /**
     * FR-03 & BR-01 & BR-02 & AC-03: Admin dapat membuat akun pengguna reguler baru dengan role 'user' dan password hash.
     */
    public function test_admin_can_create_regular_user(): void
    {
        $userData = [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'user',
        ]);

        $created = User::where('email', 'budi@example.com')->first();
        $this->assertTrue(Hash::check('password123', $created->password));
    }

    /**
     * FR-03 & BR-02 & AC-03: Admin dapat membuat akun pengguna baru dengan role 'admin'.
     */
    public function test_admin_can_create_admin_user(): void
    {
        $userData = [
            'name' => 'Co Admin',
            'email' => 'coadmin@example.com',
            'password' => 'adminpass123',
            'password_confirmation' => 'adminpass123',
            'role' => 'admin',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'coadmin@example.com',
            'role' => 'admin',
        ]);
    }

    /**
     * BR-01 & BR-10 & AC-03: Pembuatan akun ditolak jika email sudah terdaftar (duplikat).
     */
    public function test_duplicate_email_is_rejected(): void
    {
        $initialCount = User::count();

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'Duplikat User',
                'email' => $this->regularUser->email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals($initialCount, User::count());
    }

    /**
     * BR-01 & BR-10: Password minimal 8 karakter dan harus dikonfirmasi saat pembuatan akun.
     */
    public function test_password_must_be_minimum_eight_chars_and_confirmed(): void
    {
        // Kurang dari 8 karakter
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'Short Pass',
                'email' => 'short@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
                'role' => 'user',
            ]);
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'short@example.com']);

        // Konfirmasi password tidak cocok
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'Mismatch Pass',
                'email' => 'mismatch@example.com',
                'password' => 'password123',
                'password_confirmation' => 'different123',
                'role' => 'user',
            ]);
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    /**
     * BR-02 & BR-10: Hanya role 'admin' dan 'user' yang diperbolehkan.
     */
    public function test_role_must_be_admin_or_user(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'Invalid Role',
                'email' => 'invalidrole@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'superadmin',
            ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'invalidrole@example.com']);
    }

    /**
     * Section 10: Validasi gagal mempertahankan old input kecuali password.
     */
    public function test_create_user_validation_failure_preserves_old_input_except_passwords(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'Form Preserved',
                'email' => 'formpreserved@example.com',
                'role' => 'admin',
                'password' => 'short',
                'password_confirmation' => 'mismatch',
            ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertEquals('Form Preserved', session()->getOldInput('name'));
        $this->assertEquals('formpreserved@example.com', session()->getOldInput('email'));
        $this->assertEquals('admin', session()->getOldInput('role'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(session()->hasOldInput('password_confirmation'));
    }

    /**
     * FR-03 & BR-14 & AC-03: Admin dapat menghapus permanen akun lain.
     */
    public function test_admin_can_delete_other_user(): void
    {
        $targetUser = User::factory()->create([
            'email' => 'target@example.com',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $targetUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        // Pastikan row terhapus permanen dari database
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
            'email' => 'target@example.com',
        ]);
    }

    /**
     * BR-01 & AC-03: Email dari akun yang dihapus permanen dapat didaftarkan kembali.
     */
    public function test_deleted_user_email_can_be_reused(): void
    {
        $targetUser = User::factory()->create([
            'email' => 'reused@example.com',
        ]);

        // Hapus user
        $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $targetUser));

        // Buat akun baru dengan email yang sama
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'Reused Account',
                'email' => 'reused@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'reused@example.com',
            'name' => 'Reused Account',
        ]);
    }

    /**
     * FR-03 & Section 5 & AC-03: Admin tidak dapat menghapus akunnya sendiri (self-delete ditolak).
     */
    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $this->adminUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');

        // Akun admin harus tetap ada
        $this->assertDatabaseHas('users', [
            'id' => $this->adminUser->id,
            'email' => $this->adminUser->email,
        ]);
    }

    /**
     * BR-14 & BR-15 & Section 5: Menghapus user creator menghapus project miliknya, tasks, dan keanggotaan secara cascade.
     */
    public function test_deleting_creator_cascades_projects_and_tasks(): void
    {
        $creator = User::factory()->create(['email' => 'creator@example.com']);
        $collaborator = User::factory()->create(['email' => 'collab@example.com']);

        $project = Project::create([
            'name' => 'Project Cascade Test',
            'creator_id' => $creator->id,
        ]);

        $project->members()->attach([$creator->id, $collaborator->id]);

        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Sample Task in Project',
            'priority' => 'medium',
            'status' => 'not_done',
        ]);

        // Admin menghapus creator
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $creator));

        $response->assertRedirect(route('admin.users.index'));

        // Creator harus terhapus
        $this->assertDatabaseMissing('users', ['id' => $creator->id]);

        // Project harus cascade terhapus
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);

        // Task harus cascade terhapus
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);

        // Pivot membership harus cascade terhapus
        $this->assertDatabaseMissing('project_user', ['project_id' => $project->id]);

        // Collaborator akunnya sendiri tetap ada
        $this->assertDatabaseHas('users', ['id' => $collaborator->id]);
    }

    /**
     * Section 5 & Section 10 & Checklist: Rollback transaksi saat penghapusan akun gagal dan tidak ada data setengah jadi.
     */
    public function test_deletion_rolls_back_atomically_if_database_exception_occurs(): void
    {
        $targetUser = User::factory()->create(['email' => 'rollback@example.com']);
        $project = Project::create([
            'name' => 'Rollback Project',
            'creator_id' => $targetUser->id,
        ]);
        $project->members()->attach($targetUser->id);

        // Simulasi error database melalui event dispatcher model User
        User::deleting(function ($user) use ($targetUser) {
            if ($user->id === $targetUser->id) {
                throw new \Exception('Simulasi kegagalan database saat transaksi hapus akun');
            }
        });

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $targetUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');

        // Pastikan seluruh data di-rollback (user, project, dan pivot tetap utuh)
        $this->assertDatabaseHas('users', ['id' => $targetUser->id]);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('project_user', ['project_id' => $project->id, 'user_id' => $targetUser->id]);
    }
}
