<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;

class InvoiceDocSend extends Mailable
{
    use Queueable, SerializesModels;
    private $file = null;
    private $mail = null;
    private $status = null;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $file, $mail, $status)
    {
        $this->file = $file;
        $this->mail = $mail;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address($this->mail, 'CFONC send invoice coltd.com'),
            replyTo: [
                new Address($this->mail, 'LongdyHeak'),
            ],
            subject: 'invoice pdf ' . $this->status,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'admin::pages.mail.view',
            with: [
                'data' => $this->file,
                'title' => "Invoice PDF"
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        $filesData = [];
        foreach ($this->file as $item) {
            $filesData[] = Attachment::fromPath($item);
        }
        return $filesData;
    }
}
