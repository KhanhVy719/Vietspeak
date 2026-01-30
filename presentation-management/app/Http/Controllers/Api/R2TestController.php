<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class R2TestController extends Controller
{
    /**
     * Test R2 connection by uploading a simple text file
     */
    public function testUpload()
    {
        try {
            // Test upload
            $filename = 'test-' . time() . '.txt';
            $content = 'Hello from VietSpeak! R2 is working! Time: ' . now();
            
            Storage::disk('r2')->put($filename, $content);
            
            // Get URL
            $url = Storage::disk('r2')->url($filename);
            
            return response()->json([
                'success' => true,
                'message' => 'R2 upload successful!',
                'filename' => $filename,
                'url' => $url,
                'content' => $content
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * List files in R2 bucket
     */
    public function listFiles()
    {
        try {
            $files = Storage::disk('r2')->files();
            
            return response()->json([
                'success' => true,
                'files' => $files,
                'count' => count($files)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload HLS video files (for future use)
     */
    public function uploadHLS(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'lesson_id' => 'required|integer',
            'm3u8_file' => 'required|file',
            'segments' => 'required|array',
            'segments.*' => 'file'
        ]);

        try {
            $courseId = $request->course_id;
            $lessonId = $request->lesson_id;
            $basePath = "courses/{$courseId}/lessons/{$lessonId}";

            // Upload master playlist
            $m3u8File = $request->file('m3u8_file');
            $m3u8Path = "{$basePath}/master.m3u8";
            
            Storage::disk('r2')->put(
                $m3u8Path,
                file_get_contents($m3u8File->getRealPath()),
                ['ContentType' => 'application/vnd.apple.mpegurl']
            );

            // Upload segments
            $uploadedSegments = [];
            foreach ($request->file('segments') as $segment) {
                $segmentPath = "{$basePath}/{$segment->getClientOriginalName()}";
                
                Storage::disk('r2')->put(
                    $segmentPath,
                    file_get_contents($segment->getRealPath()),
                    ['ContentType' => 'video/MP2T']
                );
                
                $uploadedSegments[] = $segmentPath;
            }

            // Get master playlist URL
            $playlistUrl = Storage::disk('r2')->url($m3u8Path);

            return response()->json([
                'success' => true,
                'playlist_url' => $playlistUrl,
                'segments_count' => count($uploadedSegments)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
