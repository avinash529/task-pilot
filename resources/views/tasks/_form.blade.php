<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="title" class="field-label">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $task?->title) }}" required class="field-input">
        @error('title')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="assigned_to" class="field-label">Assign To</label>
        <select id="assigned_to" name="assigned_to" required class="field-select">
            <option value="">Select a user</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('assigned_to', $task?->assigned_to) === (string) $user->id)>{{ $user->name }} ({{ $user->role->label() }})</option>
            @endforeach
        </select>
        @error('assigned_to')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="priority" class="field-label">Priority</label>
        <select id="priority" name="priority" required class="field-select">
            @foreach ($priorities as $priority)
                <option value="{{ $priority->value }}" @selected(old('priority', $task?->priority?->value) === $priority->value)>{{ $priority->label() }}</option>
            @endforeach
        </select>
        @error('priority')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="field-label">Status</label>
        <select id="status" name="status" required class="field-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $task?->status?->value ?? 'pending') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="due_date" class="field-label">Due Date</label>
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $task?->due_date?->toDateString()) }}" required class="field-input">
        @error('due_date')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="description" class="field-label">Description</label>
        <textarea id="description" name="description" rows="7" required class="field-textarea">{{ old('description', $task?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('tasks.index') }}" class="btn-secondary">Cancel</a>
</div>
