<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wester\ChunkUpload\Chunk;
use Wester\ChunkUpload\Validation\Exceptions\ValidationException;
class VideoManagement extends Controller
{
   public function upload(Request $request){
    try {
        $chunk = new Chunk([
            'name' => 'videodfgdf', // same as    $_FILES['video']
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

            $proof_src = '/storage/videos/' . $chunk->createFileName();
           //upload complete record


            return response()->json([
                'status' => true,
                'message' => 'Video Upload SuccessFully',
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
