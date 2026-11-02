@extends('layouts.site')
@section('title', 'Newsletter unsubscribe')
@section('description', 'Manage your e-Linx newsletter subscription.')
@section('content')
<section class="section">
    <div class="container">
        <div class="cardx mx-auto" style="max-width: 680px">
            @if(session('success'))
                <h1 class="h2">Subscription cancelled</h1>
                <p>{{ session('success') }}</p>
            @elseif($subscriber->unsubscribed_at)
                <h1 class="h2">You are already unsubscribed</h1>
                <p>This email address will not receive e-Linx newsletter updates.</p>
            @else
                <h1 class="h2">Unsubscribe from e-Linx updates?</h1>
                <p>Confirm below to stop newsletter updates being sent to {{ $subscriber->email }}.</p>
                <form method="post" action="{{ route('newsletter.unsubscribe.submit', $subscriber->unsubscribe_token) }}">
                    @csrf
                    <button class="btn btn-primary rounded-pill px-4" type="submit">Confirm unsubscribe</button>
                </form>
            @endif
            <a class="btn btn-soft rounded-pill px-4 mt-3" href="{{ route('home') }}">Return to e-Linx</a>
        </div>
    </div>
</section>
@endsection
