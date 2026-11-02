@extends('layouts.admin')
@section('title', 'Leads')
@section('content')
<div class="leads-page">
    <div class="leads-page-heading">
        <div>
            <span class="leads-eyebrow"><i class="bi bi-inbox" aria-hidden="true"></i> ENQUIRY MANAGEMENT</span>
            <h1>Leads</h1>
            <p>Review contact messages and project briefs, then track each follow-up.</p>
        </div>
        <div class="tabs leads-status-tabs">
            @foreach(['' => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $key => $label)
                <a class="tab {{ $status === $key ? 'on' : '' }}" @if($status === $key) aria-current="page" @endif href="{{ route('admin.leads.index', $key ? ['status' => $key] : []) }}">
                    {{ $label }}<span>{{ $key === '' ? $statusCounts->sum() : ($status === $key ? $leads->total() : $statusCounts->get($key, 0)) }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <section class="leads-panel">
        <div class="leads-panel-heading">
            <div><h2>{{ $status ? ucfirst($status).' enquiries' : 'All enquiries' }}</h2><p>{{ $leads->total() }} {{ \Illuminate\Support\Str::plural('request', $leads->total()) }} · newest first</p></div>
            <span class="leads-panel-icon"><i class="bi bi-person-lines-fill" aria-hidden="true"></i></span>
        </div>
        @forelse($leads as $lead)
            <article class="lead-record">
                <div class="lead-record-summary">
                    <div class="lead-record-contact">
                        <span class="lead-record-avatar">{{ mb_strtoupper(mb_substr($lead->name, 0, 1)) }}</span>
                        <div class="lead-record-identity">
                            <strong>{{ $lead->name }}</strong>
                            @if($lead->company_name)<span class="lead-record-company"><i class="bi bi-building" aria-hidden="true"></i>{{ $lead->company_name }}</span>@endif
                            <a href="mailto:{{ $lead->email }}"><i class="bi bi-envelope" aria-hidden="true"></i>{{ $lead->email }}</a>
                            @if($lead->phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $lead->phone) }}"><i class="bi bi-telephone" aria-hidden="true"></i>{{ $lead->phone }}</a>@endif
                            <time datetime="{{ $lead->created_at?->toIso8601String() }}"><i class="bi bi-clock" aria-hidden="true"></i>{{ $lead->created_at?->format('M j, Y · g:i A') }}</time>
                        </div>
                    </div>
                    <div class="lead-record-request">
                        <span class="lead-record-type"><i class="bi {{ $lead->type === 'quote' ? 'bi-briefcase' : ($lead->type === 'course' ? 'bi-mortarboard' : 'bi-chat-square-text') }}" aria-hidden="true"></i>{{ $lead->type === 'quote' ? 'PROJECT REQUEST' : ($lead->type === 'course' ? 'COURSE ENQUIRY' : 'CONTACT MESSAGE') }}</span>
                        <strong>{{ $lead->subject ?: 'Website enquiry' }}</strong>
                        @if($lead->project_name)<span class="lead-record-project"><i class="bi bi-folder2-open" aria-hidden="true"></i>{{ $lead->project_name }}</span>@endif
                        @if($lead->budget)<span class="lead-record-budget"><i class="bi bi-wallet2" aria-hidden="true"></i>{{ $lead->budget }}</span>@endif
                    </div>
                    <div class="lead-record-actions">
                        <form method="post" action="{{ route('admin.leads.update', $lead) }}">
                            @csrf @method('PATCH')
                            <label class="visually-hidden" for="lead-status-{{ $lead->id }}">Status for {{ $lead->name }}</label>
                            <select id="lead-status-{{ $lead->id }}" class="select lead-status-select status-{{ $lead->status }}" name="status" onchange="this.form.submit()">
                                @foreach(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $option => $label)
                                    <option value="{{ $option }}" @selected($lead->status === $option)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                        @if($lead->email)<a class="btn btn-ghost btn-sm" href="mailto:{{ $lead->email }}"><i class="bi bi-reply" aria-hidden="true"></i>Reply</a>@endif
                        <form method="post" action="{{ route('admin.leads.delete', $lead) }}" data-confirm="Delete the lead from {{ $lead->name }}? This can't be undone.">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm lead-delete-button" type="submit" aria-label="Delete enquiry from {{ $lead->name }}"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                        </form>
                    </div>
                </div>
                <details class="lead-details">
                    <summary><span><i class="bi bi-card-text" aria-hidden="true"></i> View full {{ $lead->type === 'quote' ? 'project brief' : ($lead->type === 'course' ? 'course enquiry' : 'message') }}</span><i class="bi bi-chevron-down lead-details-chevron" aria-hidden="true"></i></summary>
                    <div class="lead-details-body">
                        <section class="lead-details-section">
                            <h3><i class="bi bi-chat-left-text" aria-hidden="true"></i> {{ $lead->type === 'quote' ? 'Project overview' : 'Message' }}</h3>
                            <p class="lead-details-message">{{ $lead->message }}</p>
                        </section>
                        @if($lead->type === 'quote' && ($lead->project_stage || $lead->timeline || $lead->target_audience || $lead->required_features || $lead->reference_url))
                            <section class="lead-details-section">
                                <h3><i class="bi bi-clipboard2-check" aria-hidden="true"></i> Scope &amp; planning</h3>
                                <dl class="lead-brief-grid">
                                    @if($lead->project_stage)<div><dt>Project stage</dt><dd>{{ $lead->project_stage }}</dd></div>@endif
                                    @if($lead->timeline)<div><dt>Preferred timeline</dt><dd>{{ $lead->timeline }}</dd></div>@endif
                                    @if($lead->target_audience)<div><dt>Who will use it?</dt><dd>{{ $lead->target_audience }}</dd></div>@endif
                                    @if($lead->required_features)<div class="lead-brief-features"><dt>Requested features</dt><dd><ul>@foreach($lead->required_features as $feature)<li><i class="bi bi-check2" aria-hidden="true"></i>{{ $feature }}</li>@endforeach</ul></dd></div>@endif
                                    @if($lead->reference_url)<div><dt>Reference link</dt><dd><a href="{{ $lead->reference_url }}" target="_blank" rel="noopener noreferrer">{{ $lead->reference_url }} <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a></dd></div>@endif
                                </dl>
                            </section>
                        @endif
                        @if($lead->service_details)
                            <section class="lead-details-section">
                                <h3><i class="bi bi-ui-checks-grid" aria-hidden="true"></i> Service-specific requirements</h3>
                                <dl class="lead-brief-grid">
                                    @foreach($lead->service_details as $key => $value)
                                        @if(filled($value))
                                            <div><dt>{{ str($key)->replace('_', ' ')->title() }}</dt><dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd></div>
                                        @endif
                                    @endforeach
                                </dl>
                            </section>
                        @endif
                    </div>
                </details>
            </article>
        @empty
            <div class="leads-empty"><span><i class="bi bi-inbox" aria-hidden="true"></i></span><h2>No {{ $status ?: '' }} enquiries yet</h2><p>New messages and project requests will appear here when someone submits a form.</p></div>
        @endforelse
        @if($leads->hasPages())<div class="leads-pagination">{{ $leads->links() }}</div>@endif
    </section>
</div>
@endsection
