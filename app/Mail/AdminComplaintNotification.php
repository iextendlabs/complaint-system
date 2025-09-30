<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\Complaint;
use Illuminate\Support\Facades\Storage;

class AdminComplaintNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Complaint Received',
            from: new Address(
                config('mail.from.address'),
                'Safe Report'
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-complaint',
            with: [
                'complaint' => $this->complaint,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->complaint->file && Storage::disk('public')->exists($this->complaint->file)) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath(Storage::disk('public')->path($this->complaint->file));
        }

        return $attachments;
    }
}
