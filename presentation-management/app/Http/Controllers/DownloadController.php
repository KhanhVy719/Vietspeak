<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Download file bài nộp với kiểm tra quyền
     */
    public function download(Submission $submission)
    {
        // Kiểm tra quyền download
        $this->authorize('download', $submission);

        // Kiểm tra file có tồn tại không
        if (!Storage::disk('private')->exists($submission->file_path)) {
            abort(404, 'File không tồn tại!');
        }

        // Lấy thông tin file
        $filePath = Storage::disk('private')->path($submission->file_path);
        $fileName = $submission->getFileName();

        // Download file
        return response()->download($filePath, $fileName);
    }
}
