<?php

namespace App\Http\Controllers\Api;

use App\Dto\SubmissionDto;
use App\Http\Requests\SubmissionCreateRequest;
use App\Jobs\SaveSubmission;

class SubmissionsController
{
    public function submit(SubmissionCreateRequest $request): array
    {
        $submission = new SubmissionDto(
            name: $request->name,
            email: $request->email,
            message: $request->message,
        );

        SaveSubmission::dispatch($submission);

        return [
            'name' => e($request->name),
            'email' => e($request->email),
            'message' => e($request->message),
        ];
    }
}
