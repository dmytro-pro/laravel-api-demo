<?php

namespace App\Listeners;

use App\Events\SubmissionSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Psr\Log\LoggerInterface;

class SubmissionSavedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected LoggerInterface $logger)
    {}

    /**
     * Handle the event.
     */
    public function handle(SubmissionSaved $event): void
    {
        $submission = $event->getSubmission();

        $this->logger->info('Submission saved', [
            'name' => $submission->name,
            'email' => $submission->email,
        ]);
    }
}
