@extends('layouts.admin')
@section('title', 'Newsletter subscribers')
@section('content')
<div class="page-head">
    <div><h1>Newsletter subscribers</h1><p>Manage people who opted in to receive occasional e-Linx updates.</p></div>
</div>
<section class="card">
    <div class="tabs">
        @foreach(['active' => 'Subscribed', 'unsubscribed' => 'Unsubscribed', 'all' => 'All'] as $key => $label)
            <a class="tab {{ $status === $key ? 'on' : '' }}" href="{{ route('admin.newsletter.index', ['status' => $key]) }}">
                {{ $label }} <span>{{ $key === 'active' ? $activeCount : ($key === 'unsubscribed' ? $unsubscribedCount : $allCount) }}</span>
            </a>
        @endforeach
    </div>
    <div class="table-wrap mt-3">
        <table>
            <thead><tr><th>Email</th><th>Subscribed</th><th>Status</th><th>Personal unsubscribe link</th><th></th></tr></thead>
            <tbody>
            @forelse($subscribers as $subscriber)
                <tr>
                    <td><a href="mailto:{{ $subscriber->email }}">{{ $subscriber->email }}</a></td>
                    <td>{{ $subscriber->subscribed_at->format('M j, Y') }}</td>
                    <td>
                        @if($subscriber->unsubscribed_at)
                            <span class="badge badge-off">Unsubscribed</span>
                        @else
                            <span class="badge badge-on">Subscribed</span>
                        @endif
                    </td>
                    <td>
                        <details>
                            <summary>Show link</summary>
                            <code>{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}</code>
                        </details>
                    </td>
                    <td class="actions">
                        <form method="post" action="{{ route('admin.newsletter.delete', $subscriber) }}" data-confirm="Remove {{ $subscriber->email }} from the newsletter list?">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="bi bi-envelope"></i>No subscribers in this list yet.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $subscribers->links() }}</div>
</section>
@endsection
