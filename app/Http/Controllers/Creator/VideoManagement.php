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
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:uploaded_videos,id'
        ]);
        UploadedVideos::where([
            'creator_id' => $request->user()->id,
            'id' => $request->id,
        ])->delete();
        return response()->json([
            'status' => true,
            'message' => 'video deleted successfully'
        ]);
    }
    public function edit(Request $request)
    {
        $update_values = array();
        if ($request->has('title')) {
            $update_values['title'] = $request->title;
        }
        if ($request->has('description')) {
            $update_values['description'] = $request->description;
        }
        if ($request->has('thumbnail')) {
            $thumbnail = Storage::put('public/videos/thumbnail', $request->file('thumbnail'));
            $update_values['thumbnail'] =  $thumbnail;
        }
        if ($request->has('privacy')) {
            $update_values['privacy'] = $request->privacy;
        }
        UploadedVideos::where([
            'creator_id' => $request->user()->id,
            'id' => $request->id,
        ])->update($update_values);
        return response()->json([
            'status' => true,
            'message' => 'Video Updated SuccessFully'
        ]);
    }
    public function video_list(Request $request)
    {
        $row = UploadedVideos::orderBy('id', 'desc')->where("creator_id", $request->user()->id)->paginate(10);
        if ($row) {
            return response()->json([
                'status' => true,
                'data' => $row,
                'message' => 'reterive done'
            ]);
        }
    }
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'max:100',
            'description' => 'max:5000'
        ]);
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
