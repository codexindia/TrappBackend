<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Wester\ChunkUpload\Chunk;
use Wester\ChunkUpload\Validation\Exceptions\ValidationException;
use App\Models\UploadedVideos;

class VideoManagement extends Controller
{
    public function upload(Request $request)
    {
        try {
            $chunk = new Chunk([
                'name' => 'video', // same as    $_FILES['video']
                'chunk_size' => 900000, // must be equal to the value specified on the client side

                // Driver
                'driver' => 'local', // [local, ftp]

                // Local driver details
                'local_driver' => [
                    'path' => public_path('/storage/videos/'), // where to upload the final file
                    'tmp_path' => public_path('/storage/videos/temp/'), // where to store the temp chunks
                ],

                // File details
                'file_name' => Chunk::RANDOM_FILE_NAME,
                'file_extension' => Chunk::ORIGINAL_FILE_EXTENSION,

                // File validation
                'validation' => ['extension:mp4,avi,3gp'],
            ]);

            $chunk->validate()->store();

            if ($chunk->isLast()) {

                $update_values = array(
                    'title' => $request->title,
                    'description' => $request->description,
                    'creator_id' => $request->user()->id,
                    'privacy' => $request->privacy
                );
                if ($request->hasFile('thumbnail')) {
                    $thumbnail = Storage::put('public/videos/thumbnail', $request->file('thumbnail'));
                    $update_values['thumbnail'] = $thumbnail;
                }


                $proof_src = '/storage/videos/' . $chunk->createFileName();
                $update_values['video_loc'] = $proof_src;
                //upload complete record
                UploadedVideos::create($update_values);



                return response()->json([
                    'status' => true,
                    'message' => 'Video Upload Success',
                    'url' => url($proof_src),
                ]);
            } else {
                $chunk->response()->json([
                    'progress' => $chunk->getProgress()
                ]);
            }
        } catch (ValidationException $e) {
            $e->response(422)->json([
                'message' => $e->getMessage(),
                'data' => $e->getErrors(),
            ]);
        }
    }
}
