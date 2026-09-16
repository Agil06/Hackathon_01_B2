<div class="card">
    <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">Tambah Anggota</h3>
    <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 1rem;">
        Tambahkan pengguna terdaftar ke daftar tugas dengan emailnya.
    </p>

    <form method="POST" action="{{ route('collaborators.store', $project) }}">
        @csrf
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="collaborator_email" class="form-label">Email Anggota</label>
            <input
                type="email"
                id="collaborator_email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="user@example.com"
                required
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Tambah Anggota</button>
    </form>
</div>
