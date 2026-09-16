@extends('layouts.app')

@section('title', 'Atur Assignee Task')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Atur Assignee</h1>
        <p class="page-subtitle">Task: <strong>{{ $task->title }}</strong></p>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul class="alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php($selectedIds = old('assignee_ids', $task->assignees->pluck('id')->all()))

            <form method="POST" action="{{ route('tasks.assignees.update', [$project, $task]) }}" class="form">
                @csrf
                @method('PUT')

                <fieldset class="form-group">
                    <legend class="form-label">Pilih anggota yang ditugaskan</legend>
                    @forelse ($members as $member)
                        <label class="form-check" for="assignee-{{ $member->id }}">
                            <input
                                id="assignee-{{ $member->id }}"
                                name="assignee_ids[]"
                                type="checkbox"
                                value="{{ $member->id }}"
                                {{ in_array($member->id, $selectedIds) ? 'checked' : '' }}
                            >
                            {{ $member->name }} <span class="text-muted">({{ $member->email }})</span>
                        </label>
                    @empty
                        <p class="text-muted">Belum ada anggota yang dapat ditugaskan.</p>
                    @endforelse
                </fieldset>

                @error('assignee_ids')
                    <span class="form-error">{{ $message }}</span>
                @enderror

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Assignee</button>
                    <a href="{{ route('tasks.show', [$project, $task]) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
