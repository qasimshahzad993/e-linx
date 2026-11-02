@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<section class="dashboard-welcome">
    <div>
        <div class="dashboard-kicker"><span class="status-dot"></span> ADMINISTRATOR OVERVIEW</div>
        <h1>Welcome back, {{ explode(' ', session('admin_name', 'Admin'))[0] }}</h1>
        <p>Here’s what’s happening across your e-Linx website.</p>
    </div>
    <div class="dashboard-actions">
        <span class="dashboard-date"><i class="bi bi-calendar3"></i>{{ now()->format('l, F j, Y') }}</span>
        <a class="btn btn-primary" href="{{ route('admin.leads.index') }}"><i class="bi bi-inbox"></i> Review leads</a>
    </div>
</section>

<section class="dashboard-stats" aria-label="Website overview">
    @foreach([
        ['New leads', $newLeads, 'bi-inbox', 'mint', 'Needs your attention', route('admin.leads.index', ['status' => 'new'])],
        ['All leads', $leadCount, 'bi-people', 'blue', $weeklyLeads.' received this week', route('admin.leads.index')],
        ['Services', $serviceCount, 'bi-layers', 'violet', 'Published offerings', route('admin.content.index', 'services')],
        ['Products', $productCount, 'bi-box-seam', 'amber', 'Catalog items', route('admin.content.index', 'products')],
        ['Projects', $projectCount, 'bi-briefcase', 'rose', 'Portfolio entries', route('admin.content.index', 'portfolio')],
        ['Courses', $courseCount, 'bi-mortarboard', 'blue', 'Training outlines', route('admin.courses.index')],
    ] as [$label, $count, $icon, $tone, $caption, $url])
        <a class="dashboard-stat" href="{{ $url }}">
            <span class="dashboard-stat-icon {{ $tone }}"><i class="bi {{ $icon }}"></i></span>
            <span class="dashboard-stat-copy"><span>{{ $label }}</span><strong>{{ number_format($count) }}</strong><small>{{ $caption }}</small></span>
            <i class="bi bi-arrow-up-right dashboard-stat-arrow" aria-hidden="true"></i>
        </a>
    @endforeach
</section>

<section class="dashboard-content-grid">
    <div class="dashboard-panel recent-leads-panel">
        <div class="dashboard-panel-heading">
            <div>
                <span class="panel-eyebrow">INBOX</span>
                <h2>Recent enquiries</h2>
                <p>Your latest contact messages and project requests.</p>
            </div>
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.leads.index') }}">View all <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-wrap">
            <table class="dashboard-leads-table">
                <thead><tr><th>Contact</th><th>Request</th><th>Status</th><th>Received</th></tr></thead>
                <tbody>
                @forelse($recentLeads as $lead)
                    <tr>
                        <td>
                            <div class="lead-identity">
                                <span class="lead-avatar">{{ mb_strtoupper(mb_substr($lead->name, 0, 1)) }}</span>
                                <span><strong>{{ $lead->name }}</strong><small>{{ $lead->email }}</small></span>
                            </div>
                        </td>
                        <td>
                            <strong class="lead-subject">{{ $lead->subject ?: 'Website enquiry' }}</strong>
                            <small class="lead-kind">{{ $lead->type === 'quote' ? 'Project quote' : ($lead->type === 'course' ? 'Course enquiry' : 'Contact message') }}</small>
                        </td>
                        <td><span class="lead-status {{ $lead->status }}">{{ ucfirst($lead->status) }}</span></td>
                        <td><span class="lead-date">{{ $lead->created_at?->format('M j, Y') }}</span><small class="lead-time">{{ $lead->created_at?->format('g:i A') }}</small></td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="dashboard-empty"><span><i class="bi bi-inbox"></i></span><strong>Your inbox is clear</strong><p>New contact messages and quote requests will show up here.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($recentLeads->isNotEmpty())
            <a class="dashboard-mobile-view-all" href="{{ route('admin.leads.index') }}">View all enquiries <i class="bi bi-arrow-right"></i></a>
        @endif
    </div>

    <aside class="dashboard-side-column">
        <div class="dashboard-panel attention-panel">
            <span class="panel-eyebrow">LEAD PIPELINE</span>
            <h2>Follow up with care.</h2>
            <p>Stay on top of enquiries and keep conversations moving.</p>
            <div class="pipeline-row"><span><i class="bi bi-circle-fill status-new"></i> Awaiting review</span><strong>{{ number_format($newLeads) }}</strong></div>
            <div class="pipeline-row"><span><i class="bi bi-circle-fill status-contacted"></i> Contacted</span><strong>{{ number_format($contactedLeads) }}</strong></div>
            <a class="panel-link" href="{{ route('admin.leads.index') }}">Open lead inbox <i class="bi bi-arrow-up-right"></i></a>
        </div>
        <div class="dashboard-quick-links">
            <span class="panel-eyebrow">QUICK ACCESS</span>
            <a href="{{ route('admin.content.index', 'services') }}"><span class="quick-link-icon"><i class="bi bi-layers"></i></span><span><strong>Manage services</strong><small>Update your offerings</small></span><i class="bi bi-chevron-right"></i></a>
            <a href="{{ route('admin.content.index', 'portfolio') }}"><span class="quick-link-icon"><i class="bi bi-briefcase"></i></span><span><strong>Update portfolio</strong><small>Showcase recent work</small></span><i class="bi bi-chevron-right"></i></a>
            <a href="{{ route('admin.courses.index') }}"><span class="quick-link-icon"><i class="bi bi-mortarboard"></i></span><span><strong>Manage courses</strong><small>Publish training outlines</small></span><i class="bi bi-chevron-right"></i></a>
            <a href="{{ route('home') }}" target="_blank" rel="noopener"><span class="quick-link-icon"><i class="bi bi-globe2"></i></span><span><strong>View website</strong><small>See the public site</small></span><i class="bi bi-chevron-right"></i></a>
        </div>
    </aside>
</section>
@endsection
