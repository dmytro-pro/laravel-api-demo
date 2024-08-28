<?php

namespace App\Http\Controllers\Api;

use App\Concerns\LoggerAware;
use App\Dto\SubmissionDto;
use App\Http\Requests\SubmissionCreateRequest;
use App\Jobs\SaveSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SubmissionsController
{
    use LoggerAware;

    public function submit(SubmissionCreateRequest $request)
    {
        try {
            $submission = new SubmissionDto(
                name: $request->name,
                email: $request->email,
                message: $request->message,
            );

            SaveSubmission::dispatch($submission);
        } catch (\Throwable $exception) {
            $response = [
                'error' => 'Internal Server Error',
                'exception_class' => get_class($exception),
                'exception_message' => $exception->getMessage(),
            ];

            $this->getLogger()->error(
                'Error during submission dispatch',
                $response + ['payload' => $submission?->toArray() ?? []]
            );

            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return [
            'name' => e($request->name),
            'email' => e($request->email),
            'message' => e($request->message),
        ];
    }
}
