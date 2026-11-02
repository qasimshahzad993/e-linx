@extends('layouts.admin')
@section('title', $type === 'faqs' ? 'FAQs' : 'Testimonials')
@section('content')
@php($isFaq = $type === 'faqs')
<div class="page-head"><div><h1>{{ $isFaq ? 'FAQs' : 'Testimonials' }}</h1><p>{{ $isFaq ? 'Answer common questions from prospective clients.' : 'Publish only customer feedback you have permission to share.' }}</p></div></div>
<div class="grid-2">
    <section class="card">
        <h2>{{ $edit ? 'Edit '.($isFaq ? 'FAQ' : 'testimonial') : 'Add '.($isFaq ? 'FAQ' : 'testimonial') }}</h2>
        <form method="post" action="{{ route('admin.trust.store', $type) }}">@csrf
            <input type="hidden" name="id" value="{{ $edit?->id ?? 0 }}">
            @if($isFaq)
                <div class="field"><label for="question">Question</label><input class="input" id="question" name="question" required maxlength="255" value="{{ old('question', $edit?->question) }}"></div>
                <div class="field"><label for="answer">Answer</label><textarea class="textarea" id="answer" name="answer" required maxlength="10000">{{ old('answer', $edit?->answer) }}</textarea><div class="hint">Use clear, accurate information about e-Linx and its services.</div></div>
            @else
                <div class="field"><label for="client_name">Client name</label><input class="input" id="client_name" name="client_name" required maxlength="120" value="{{ old('client_name', $edit?->client_name) }}"></div>
                <div class="field"><label for="client_role">Role or title</label><input class="input" id="client_role" name="client_role" maxlength="160" value="{{ old('client_role', $edit?->client_role) }}"></div>
                <div class="field"><label for="company_name">Company or organization</label><input class="input" id="company_name" name="company_name" maxlength="180" value="{{ old('company_name', $edit?->company_name) }}"></div>
                <div class="field"><label for="service">Related service</label><input class="input" id="service" name="service" maxlength="160" value="{{ old('service', $edit?->service) }}"></div>
                <div class="field"><label for="quote">Customer feedback</label><textarea class="textarea" id="quote" name="quote" required maxlength="3000">{{ old('quote', $edit?->quote) }}</textarea><div class="hint">Publish only accurate feedback with the customer's permission.</div></div>
            @endif
            <div class="field"><label for="sort_order">Display order</label><input class="input" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $edit?->sort_order ?? 0) }}"></div>
            <label class="check"><input type="checkbox" name="status" value="1" @checked(old('status', $edit?->status ?? $isFaq))>Show on website</label>
            <div class="btn-row"><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i>{{ $edit ? 'Save changes' : 'Add '.($isFaq ? 'FAQ' : 'testimonial') }}</button>@if($edit)<a class="btn btn-ghost" href="{{ route('admin.trust.index', $type) }}">Cancel</a>@endif</div>
        </form>
    </section>
    <section class="card">
        <div class="card-head"><h2>All {{ $isFaq ? 'FAQs' : 'testimonials' }} ({{ $items->count() }})</h2></div>
        <div class="table-wrap"><table><thead><tr><th>{{ $isFaq ? 'Question' : 'Client' }}</th>@unless($isFaq)<th>Feedback</th>@endunless<th>Status</th><th></th></tr></thead><tbody>
        @forelse($items as $item)
            <tr>
                <td><strong>{{ $isFaq ? $item->question : $item->client_name }}</strong>@unless($isFaq)<small class="d-block text-secondary">{{ $item->company_name ?: $item->client_role ?: '—' }}</small>@endunless</td>
                @unless($isFaq)<td>{{ \Illuminate\Support\Str::limit($item->quote, 100) }}</td>@endunless
                <td><span class="badge {{ $item->status ? 'badge-on' : 'badge-off' }}">{{ $item->status ? 'Live' : 'Hidden' }}</span></td>
                <td class="actions"><a class="btn btn-ghost btn-sm" href="{{ route('admin.trust.index', ['type' => $type, 'edit' => $item->id]) }}"><i class="bi bi-pencil"></i>Edit</a><form method="post" action="{{ route('admin.trust.delete', [$type, $item->id]) }}" data-confirm="Delete this {{ $isFaq ? 'FAQ' : 'testimonial' }}? This can't be undone.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Delete</button></form></td>
            </tr>
        @empty
            <tr><td colspan="{{ $isFaq ? 3 : 4 }}"><div class="empty"><i class="bi {{ $isFaq ? 'bi-question-circle' : 'bi-chat-quote' }}"></i>No {{ $isFaq ? 'FAQs' : 'testimonials' }} yet.</div></td></tr>
        @endforelse
        </tbody></table></div>
    </section>
</div>
@endsection
