<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ExceptionController extends Controller
{
    public function __invoke(): Response
    {
        throw new \RuntimeException('This exception is intentional! Use AI to fix it.');

        return Inertia::render('demo/Exception');
    }
}
