<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Mail;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;
    private $orderUrl = "https://laravel.com/docs/10.x/scheduling";
    private $file = null;
    private $mail = null;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $file, $mail)
    {
        $this->file = $file;
        $this->mail = $mail;
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
            subject: 'invoice pdf by cfonc',
        );
        // return new Envelope(
        //     from: new Address($this->mail, 'CFONC send invoice coltd.com'),
        //     subject: 'CFONC send invoice coltd.com',
        // );
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
