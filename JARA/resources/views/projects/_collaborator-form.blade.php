<div class="card">
    <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">Add Collaborator</h3>
    <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 1rem;">
        Add an existing registered user to collaborate on this project by entering their email address.
    </p>

    <form method="POST" action="{{ route('collaborators.store', $project) }}">
        @csrf
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="collaborator_email" class="form-label">Collaborator Email</label>
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

        <button type="submit" class="btn btn-primary" style="width: 100%;">Add Collaborator</button>
    </form>
</div>
