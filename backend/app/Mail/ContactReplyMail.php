<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactMessage $msg;
    public string $replyText;
    public string $storeName;

    public function __construct(ContactMessage $msg, string $replyText, string $storeName = 'ZadStore')
    {
        $this->msg = $msg;
        $this->replyText = $replyText;
        $this->storeName = $storeName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رد على رسالتك - ' . $this->storeName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_reply',
        );
    }
}
