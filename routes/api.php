<?php

use App\Http\Controllers\Api\SubmissionsController;
use Illuminate\Support\Facades\Route;

Route::post('/submit', SubmissionsController::class . '@submit');
