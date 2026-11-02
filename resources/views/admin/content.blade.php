@extends('layouts.admin')
@section('title', ucfirst($type))
@section('content')
@php($singular = match($type) { 'services' => 'service', 'products' => 'product', default => 'project' })
<div class="page-head"><div><h1>{{ ucfirst($type) }}</h1><p>{{ ucfirst($type) }} listed on your website.</p></div></div>
<div class="grid-2">
    <section class="card">
        <h2>{{ $edit ? 'Edit '.$singular : 'Add '.$singular }}</h2>
        <form method="post" action="{{ route('admin.content.store', $type) }}">@csrf
            <input type="hidden" name="id" value="{{ $edit?->id ?? 0 }}">
            @if($type === 'products')
                <div class="field"><label for="name">Product name</label><input class="input" id="name" name="name" required maxlength="160" value="{{ old('name', $edit?->name) }}"></div>
                <div class="field"><label for="category">Category</label><input class="input" id="category" name="category" maxlength="100" value="{{ old('category', $edit?->category) }}"></div>
            @else
                <div class="field"><label for="title">{{ $type === 'services' ? 'Service title' : 'Project title' }}</label><input class="input" id="title" name="title" required maxlength="{{ $type === 'services' ? 160 : 180 }}" value="{{ old('title', $edit?->title) }}"></div>
                @if($type === 'portfolio')<div class="field"><label for="category">Category</label><input class="input" id="category" name="category" required maxlength="100" placeholder="Web, Mobile, SaaS, SEO or Campaign" value="{{ old('category', $edit?->category) }}"></div>@endif
                @if($type === 'services')
                    <div class="field"><label for="category">Navigation category</label><select class="select" id="category" name="category"><option value="">Choose a category</option>@foreach(\App\Models\Service::CATEGORIES as $category => $slug)<option value="{{ $category }}" @selected(old('category', $edit?->category) === $category)>{{ $category }}</option>@endforeach</select><div class="hint">This controls the public navigation menu and service catalogue section.</div></div>
                    <div class="field"><label for="short_description">Short description</label><input class="input" id="short_description" name="short_description" maxlength="255" value="{{ old('short_description', $edit?->short_description) }}"></div>
                    <div class="field"><label for="sort_order">Display order</label><input class="input" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $edit?->sort_order ?? 0) }}"></div>
                @endif
            @endif
            <div class="field"><label for="description">Description</label><textarea class="textarea" id="description" name="description" maxlength="10000">{{ old('description', $edit?->description) }}</textarea></div>
            @if($type === 'portfolio')
                <p class="hint">Optional case study details. Publish only approved, verifiable information and never disclose confidential client data.</p>
                <div class="field"><label for="challenge">Client challenge</label><textarea class="textarea" id="challenge" name="challenge" maxlength="10000">{{ old('challenge', $edit?->challenge) }}</textarea></div>
                <div class="field"><label for="solution">Our approach</label><textarea class="textarea" id="solution" name="solution" maxlength="10000">{{ old('solution', $edit?->solution) }}</textarea></div>
                <div class="field"><label for="outcome">Outcome (optional)</label><textarea class="textarea" id="outcome" name="outcome" maxlength="10000">{{ old('outcome', $edit?->outcome) }}</textarea></div>
                <div class="field"><label for="url">Project link</label><input class="input" id="url" name="url" type="url" maxlength="500" placeholder="https://..." value="{{ old('url', $edit?->url) }}"></div>
            @endif
            <div class="field"><label for="icon">Icon class</label><input class="input" id="icon" name="icon" maxlength="100" placeholder="bi bi-window" value="{{ old('icon', $edit?->icon ?: 'bi bi-gear') }}"><div class="hint">Any Bootstrap Icons class.</div></div>
            <label class="check"><input type="checkbox" name="status" value="1" @checked(old('status', $edit?->status ?? true))>Show on website</label>
            <div class="btn-row"><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i>{{ $edit ? 'Save changes' : 'Add '.$singular }}</button>@if($edit)<a class="btn btn-ghost" href="{{ route('admin.content.index', $type) }}">Cancel</a>@endif</div>
        </form>
    </section>
    <section class="card">
        <div class="card-head"><h2>All {{ $type }} ({{ $items->count() }})</h2></div>
        <div class="table-wrap"><table><thead><tr><th>{{ $type === 'products' ? 'Name' : 'Title' }}</th>@if($type !== 'services')<th>Category</th>@else<th>Navigation category</th>@endif<th>Status</th><th></th></tr></thead><tbody>
        @forelse($items as $item)
            <tr><td><strong>{{ $item->title ?? $item->name }}</strong></td><td>{{ $item->category ?: ($type === 'services' ? 'Uncategorized' : '-') }}</td><td><span class="badge {{ $item->status ? 'badge-on' : 'badge-off' }}">{{ $item->status ? 'Live' : 'Hidden' }}</span></td><td class="actions"><a class="btn btn-ghost btn-sm" href="{{ route('admin.content.index', ['type' => $type, 'edit' => $item->id]) }}"><i class="bi bi-pencil"></i>Edit</a><form method="post" action="{{ route('admin.content.delete', [$type, $item->id]) }}" data-confirm="Delete “{{ $item->title ?? $item->name }}”? This can't be undone.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Delete</button></form></td></tr>
        @empty
            <tr><td colspan="4"><div class="empty"><i class="bi bi-layers"></i>No {{ $type }} yet.</div></td></tr>
        @endforelse
        </tbody></table></div>
    </section>
</div>
@endsection
