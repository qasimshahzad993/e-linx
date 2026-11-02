@extends('layouts.admin')
@section('title', 'Training courses')
@section('content')
<div class="page-head"><div><h1>Training courses</h1><p>Draft course outlines stay private until you verify the schedule and publish them.</p></div></div>
<div class="grid-2">
    <section class="card">
        <h2>{{ $edit ? 'Edit course' : 'Add course' }}</h2>
        <form method="post" action="{{ route('admin.courses.store') }}">@csrf
            <input type="hidden" name="id" value="{{ $edit?->id ?? 0 }}">
            <div class="field"><label for="title">Course title</label><input class="input" id="title" name="title" required maxlength="180" value="{{ old('title', $edit?->title) }}"></div>
            <div class="field"><label for="category">Category</label><input class="input" id="category" name="category" maxlength="100" value="{{ old('category', $edit?->category) }}"></div>
            <div class="field"><label for="summary">Short summary</label><input class="input" id="summary" name="summary" required maxlength="320" value="{{ old('summary', $edit?->summary) }}"></div>
            <div class="field"><label for="description">Description</label><textarea class="textarea" id="description" name="description" required maxlength="10000">{{ old('description', $edit?->description) }}</textarea></div>
            <div class="field"><label for="curriculum">Curriculum (one item per line)</label><textarea class="textarea" id="curriculum" name="curriculum" maxlength="10000">{{ old('curriculum', $edit?->curriculum) }}</textarea></div>
            <div class="field"><label for="duration">Duration</label><input class="input" id="duration" name="duration" maxlength="100" placeholder="Add confirmed duration" value="{{ old('duration', $edit?->duration) }}"></div>
            <div class="field"><label for="delivery_mode">Delivery mode</label><input class="input" id="delivery_mode" name="delivery_mode" maxlength="100" placeholder="Online, in person, or blended" value="{{ old('delivery_mode', $edit?->delivery_mode) }}"></div>
            <div class="field"><label for="price">Price</label><input class="input" id="price" name="price" maxlength="100" placeholder="Add confirmed price, or leave blank" value="{{ old('price', $edit?->price) }}"></div>
            <div class="field"><label for="icon">Bootstrap icon class</label><input class="input" id="icon" name="icon" maxlength="100" placeholder="bi bi-mortarboard" value="{{ old('icon', $edit?->icon ?: 'bi bi-mortarboard') }}"></div>
            <div class="field"><label for="sort_order">Display order</label><input class="input" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $edit?->sort_order ?? 0) }}"></div>
            <label class="check"><input type="checkbox" name="status" value="1" @checked(old('status', $edit?->status ?? false))>Publish on website</label>
            <div class="btn-row"><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i>{{ $edit ? 'Save changes' : 'Add course' }}</button>@if($edit)<a class="btn btn-ghost" href="{{ route('admin.courses.index') }}">Cancel</a>@endif</div>
        </form>
    </section>
    <section class="card">
        <div class="card-head"><h2>All courses ({{ $courses->count() }})</h2></div>
        <div class="table-wrap"><table><thead><tr><th>Course</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($courses as $course)
            <tr><td><strong>{{ $course->title }}</strong><small class="d-block text-secondary">{{ $course->category ?: 'Uncategorized' }}</small></td><td><span class="badge {{ $course->status ? 'badge-on' : 'badge-off' }}">{{ $course->status ? 'Published' : 'Draft' }}</span></td><td class="actions"><a class="btn btn-ghost btn-sm" href="{{ route('admin.courses.index', ['edit' => $course->id]) }}"><i class="bi bi-pencil"></i>Edit</a><form method="post" action="{{ route('admin.courses.delete', $course) }}" data-confirm="Delete “{{ $course->title }}”? This can't be undone.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Delete</button></form></td></tr>
        @empty
            <tr><td colspan="3"><div class="empty"><i class="bi bi-mortarboard"></i>No courses yet.</div></td></tr>
        @endforelse
        </tbody></table></div>
    </section>
</div>
@endsection
