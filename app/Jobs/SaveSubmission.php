<?php

namespace App\Jobs;

use App\Concerns\EventsAware;
use App\Dto\SubmissionDto;
use App\Events\SubmissionSaved;
use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveSubmission implements ShouldQueue
{
    use Dispatchable, Queueable, EventsAware;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SubmissionDto $submissionDto,
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $submission = new Submission($this->submissionDto->toArray());
        $submission->saveOrFail();

        $this->getEvents()->dispatch(new SubmissionSaved($submission));
    }
}
