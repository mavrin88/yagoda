<?php

    namespace App\Mail;

    use Illuminate\Bus\Queueable;
    use Illuminate\Mail\Mailable;
    use Illuminate\Mail\Mailables\Address;
    use Illuminate\Mail\Mailables\Content;
    use Illuminate\Mail\Mailables\Envelope;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\Log;

    class SendEmail extends Mailable
    {
        use Queueable, SerializesModels;

        public array $data;
        public $subject;
        public string $emailFrom;

        /**
         * Create a new message instance.
         */
        public function __construct(array $data, string $subject, string $emailFrom = null)
        {
            $this->data = $data;
            $this->subject = $subject;
            $this->emailFrom = $emailFrom ?: config('mail.from.address');

        }

        /**
         * Get the message envelope.
         */
        public function envelope(): Envelope
        {
            return new Envelope(
                from: new Address($this->emailFrom),
                subject: $this->subject,
            );
        }

        /**
         * Get the message content definition.
         */
        public function content(): Content
        {
            return new Content(
                view: 'admin-mail',
                with: [
                    'subject' => $this->subject,
                    'data' => $this->data,
                ],
            );
        }
    }
