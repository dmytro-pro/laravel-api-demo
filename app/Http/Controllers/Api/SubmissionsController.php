<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SubmissionCreateRequest;

class SubmissionsController
{
    public function submit(SubmissionCreateRequest $request): array
    {
        return [
            'name' => e($request->name),
            'email' => e($request->email),
            'message' => e($request->message),
        ];
    }
}
