<?php

namespace App\Console\Commands;

use App\Mail\SimpleTestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to the specified address to verify mail configuration.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = $this->argument('email');

        $this->info("Attempting to send a test email to: {$email}...");

        try {
            Mail::to($email)->send(new SimpleTestMail());
            $this->info("Test email sent successfully! Please check your inbox (or logs if using log driver).");
        } catch (\Exception $e) {
            $this->error("Failed to send test email: " . $e->getMessage());
        }
    }
}
