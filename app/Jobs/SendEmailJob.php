<?php

    namespace App\Jobs;

    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Foundation\Queue\Queueable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Mail;
    use App\Mail\SendEmail;

    class SendEmailJob implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        public string $emailTo;
        public SendEmail $template;

        /**
         * Create a new job instance.
         */
        public function __construct(string $emailTo, SendEmail $template)
        {
            $this->emailTo = $emailTo;
            $this->template = $template;
        }

        /**
         * Execute the job.
         */
        public function handle(): void
        {
            try {
                Log::info('Sending mail to: ' . $this->emailTo);

                Mail::to($this->emailTo)->send($this->template);

                Log::info('Mail successfully sent to: ' . $this->emailTo);
            } catch (\Exception $e) {
                Log::error('Mail Sending Failed: ' . $e->getMessage());
            }
        }
    }
