<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ObjectStorageController extends Controller
{
    public function index(): Response
    {
        $files = collect(Storage::files('demo'))
            ->map(fn (string $path) => [
                'name' => basename($path),
                'path' => $path,
                'size' => Storage::size($path),
                'lastModified' => date('Y-m-d H:i:s', Storage::lastModified($path)),
                'url' => Storage::url($path),
            ]);

        return Inertia::render('demo/ObjectStorage', [
            'files' => $files->values(),
            'diskDriver' => config('filesystems.default'),
        ]);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $request->file('file')->store('demo');

        return back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        Storage::delete($request->input('path'));

        return back();
    }
}
