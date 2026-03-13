<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $task?->title) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
        @error('title')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="assigned_to" class="mb-2 block text-sm font-medium text-slate-700">Assign To</label>
        <select id="assigned_to" name="assigned_to" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
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
        <label for="priority" class="mb-2 block text-sm font-medium text-slate-700">Priority</label>
        <select id="priority" name="priority" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
            @foreach ($priorities as $priority)
                <option value="{{ $priority->value }}" @selected(old('priority', $task?->priority?->value) === $priority->value)>{{ $priority->label() }}</option>
            @endforeach
        </select>
        @error('priority')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $task?->status?->value ?? 'pending') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="due_date" class="mb-2 block text-sm font-medium text-slate-700">Due Date</label>
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $task?->due_date?->toDateString()) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
        @error('due_date')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
        <textarea id="description" name="description" rows="7" required class="w-full rounded-3xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">{{ old('description', $task?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $submitLabel }}</button>
    <a href="{{ route('tasks.index') }}" class="rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Cancel</a>
</div>
