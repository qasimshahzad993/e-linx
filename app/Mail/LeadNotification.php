<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->lead->type === 'quote' ? 'New project request from website' : 'New website enquiry',
            replyTo: [new Address($this->lead->email, $this->lead->name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-notification');
    }
}
