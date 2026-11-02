<!doctype html>
<html lang="en">
<body style="margin:0;padding:24px;background:#f3f6fa;color:#172235;font-family:Arial,sans-serif">
    <main style="max-width:680px;margin:0 auto;padding:28px;background:#fff;border:1px solid #dce4ee;border-radius:12px">
        <p style="margin:0 0 8px;color:#078eae;font-size:13px;font-weight:bold;text-transform:uppercase">e-Linx website notification</p>
        <h1 style="margin:0 0 20px;font-size:24px">{{ $lead->type === 'quote' ? 'New project request' : 'New website enquiry' }}</h1>
        <table role="presentation" style="width:100%;border-collapse:collapse">
            <tr><th align="left" style="padding:8px 12px 8px 0">Name</th><td style="padding:8px 0">{{ $lead->name }}</td></tr>
            <tr><th align="left" style="padding:8px 12px 8px 0">Email</th><td style="padding:8px 0"><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td></tr>
            @if($lead->phone)<tr><th align="left" style="padding:8px 12px 8px 0">Phone</th><td style="padding:8px 0">{{ $lead->phone }}</td></tr>@endif
            @if($lead->company_name)<tr><th align="left" style="padding:8px 12px 8px 0">Company</th><td style="padding:8px 0">{{ $lead->company_name }}</td></tr>@endif
            @if($lead->subject)<tr><th align="left" style="padding:8px 12px 8px 0">Request</th><td style="padding:8px 0">{{ $lead->subject }}</td></tr>@endif
            @if($lead->project_name)<tr><th align="left" style="padding:8px 12px 8px 0">Project</th><td style="padding:8px 0">{{ $lead->project_name }}</td></tr>@endif
            @if($lead->project_stage)<tr><th align="left" style="padding:8px 12px 8px 0">Stage</th><td style="padding:8px 0">{{ $lead->project_stage }}</td></tr>@endif
            @if($lead->timeline)<tr><th align="left" style="padding:8px 12px 8px 0">Timeline</th><td style="padding:8px 0">{{ $lead->timeline }}</td></tr>@endif
            @if($lead->budget)<tr><th align="left" style="padding:8px 12px 8px 0">Budget</th><td style="padding:8px 0">{{ $lead->budget }}</td></tr>@endif
            @if($lead->target_audience)<tr><th align="left" style="padding:8px 12px 8px 0">Audience</th><td style="padding:8px 0">{{ $lead->target_audience }}</td></tr>@endif
            @if($lead->required_features)<tr><th align="left" style="padding:8px 12px 8px 0">Features</th><td style="padding:8px 0">{{ implode(', ', $lead->required_features) }}</td></tr>@endif
            @if($lead->reference_url)<tr><th align="left" style="padding:8px 12px 8px 0">Reference</th><td style="padding:8px 0"><a href="{{ $lead->reference_url }}">{{ $lead->reference_url }}</a></td></tr>@endif
            @if($lead->service_details)<tr><th align="left" style="padding:8px 12px 8px 0">Service details</th><td style="padding:8px 0">@foreach($lead->service_details as $key => $value) @if(filled($value)){{ str($key)->replace('_', ' ')->title() }}: {{ is_array($value) ? implode(', ', $value) : $value }}<br>@endif @endforeach</td></tr>@endif
        </table>
        <h2 style="margin:24px 0 8px;font-size:17px">Message</h2>
        <p style="margin:0;line-height:1.6;white-space:pre-line">{{ $lead->message }}</p>
        <p style="margin:24px 0 0;color:#637188;font-size:13px">This request is also available in the e-Linx admin leads inbox.</p>
    </main>
</body>
</html>
