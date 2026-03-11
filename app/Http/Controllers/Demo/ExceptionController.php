<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ExceptionController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('demo/Exception');
    }
}
