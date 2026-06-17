<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Download a stored application document (admin only).
     *
     * The path is base64-encoded so we never expose raw storage paths in URLs,
     * and we validate that the decoded path lives inside the expected directory.
     */
    public function download(Request $request, string $path): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        $decoded = base64_decode($path, strict: true);

        if ($decoded === false) {
            abort(400, 'Invalid document reference.');
        }

        // Restrict to the applications/documents directory
        if (!str_starts_with($decoded, 'applications/documents/')) {
            abort(403, 'Access denied.');
        }

        if (!Storage::disk('public')->exists($decoded)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($decoded);
    }
}
