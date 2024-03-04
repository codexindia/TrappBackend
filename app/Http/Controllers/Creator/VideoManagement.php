<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\VideoAnalytics;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Wester\ChunkUpload\Chunk;
use Wester\ChunkUpload\Validation\Exceptions\ValidationException;
use App\Models\UploadedVideos;
use App\Models\Category;

class VideoManagement extends Controller
{
    private  $baseurl;

    public function __construct()
    {
        if (env('VIDEO_API_ENV') == 'sandbox')
            $this->baseurl = 'sandbox.api.video';
        else
            $this->baseurl = 'ws.api.video';
    }
    public function get_cat_list(Request $request)
    {
        $data = Category::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'cat retreive'
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:uploaded_videos,id'
        ]);
        $vid = UploadedVideos::where([
            'creator_id' => $request->user()->id,
            'id' => $request->id,
        ])->first();
        Storage::delete([$vid->video_loc, $vid->thumbnail]);
        $vid->delete();
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
        if ($request->has('playlist_id')) {
            $update_values['playlist_id'] = $request->playlist_id;
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
                    'privacy' => $request->privacy,
                );
                if ($request->hasFile('thumbnail')) {
                    $thumbnail = Storage::put('public/videos/thumbnail', $request->file('thumbnail'));
                    $update_values['thumbnail'] = $thumbnail;
                }
                if ($request->has('cat_id')) {
                    $update_values['cat_id'] = $request->cat_id;
                }
                if ($request->has('playlist_id')) {
                    $update_values['playlist_id'] = $request->playlist_id;
                }
                $proof_src = 'videos/' . $chunk->createFileName();
                $update_values['video_loc'] = $proof_src;
                //upload complete record
                $result = UploadedVideos::create($update_values);

                $media = FFMpeg::open('//public/' . $result->getRawOriginal('video_loc'));
                $durationInSeconds = $media->getDurationInSeconds(); // returns an int
                $hours = floor($durationInSeconds / 3600);
                $mins = floor(($durationInSeconds - ($hours * 3600)) / 60);
             
                UploadedVideos::find($result->id)->update([
                    'video_duration' => json_encode([
                        'minute' => $mins,
                        'hours' => $hours,
                    ]),
                ]);

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

    public function create_live(Request $request)
    {


        $request->validate([
            'title' => 'required|min:6|max:50',
            'description' => 'min:6|max:5000',
            'privacy' => 'required|in:public,private',
            'thumbnail' => 'required|image'
        ]); {

            $response = Http::withBasicAuth(env('VIDEO_API'), 'username')->post('https://' . $this->baseurl . '/live-streams', [
                'name' => $request->title,
            ]);
            if ($response) {

                $update_values = array(
                    'title' => $request->title,
                    'description' => $request->description,
                    'creator_id' => $request->user()->id,
                    'privacy' => 'private',
                    'video_type' => 'live',
                    'live_api_data' => $response,


                );
                if ($request->hasFile('thumbnail')) {
                    $thumbnail = Storage::put('public/videos/thumbnail', $request->file('thumbnail'));
                    $update_values['thumbnail'] = $thumbnail;
                }
                if ($request->has('cat_id')) {
                    $update_values['cat_id'] = $request->cat_id;
                }


                //upload complete record

                $response = json_decode($response);
                $update_values['video_loc'] = $response->assets->hls;
                $result = UploadedVideos::create($update_values);
                $thumbnail = Http::withBasicAuth(env('VIDEO_API'), 'username')->attach(
                    'file',
                    $request->file('thumbnail'),
                    'photo.jpg'
                )->post('https://' . $this->baseurl . '/live-streams/' . $response->liveStreamId . '/thumbnail');
            }
            return response()->json([
                'status' => true,
                'data' => array(
                    'vid_id' => $result->id,
                    'stream_id' => $response->liveStreamId,
                    'streamKey' => $response->streamKey,
                    'streamUrl' => 'rtmp://broadcast.api.video/s',
                    'broadcasting' => $response->broadcasting,
                    'hls_player' => $response->assets->hls
                ),
                'message' => 'stream created successFully'
            ]);
        }
    }
    public function fetch_live(Request $request)
    {
        $request->validate([
            'vid_id' => 'required|exists:uploaded_videos,id'
        ]);
        $data = UploadedVideos::find($request->vid_id);
        $static = $data;
        if ($data->video_type != 'live') {
            return "video not live video";
        }
        $liveStreamId = json_decode($data->live_api_data)->liveStreamId;
        $response = Http::withBasicAuth(env('VIDEO_API'), 'username')->get('https://' . $this->baseurl . '/live-streams/' . $liveStreamId);
        if (isset(json_decode($response)->liveStreamId)) {
            $data->update([
                'live_api_data' => $response
            ]);
            $like_count = VideoAnalytics::where([
                'action' => 'like',
            ])->whereJsonContains('attribute', ['video_id' => $request->vid_id])->count();

            $response = json_decode($response);
            return response()->json([
                'status' => true,
                'data' => array(
                    'stream_id' => $response->liveStreamId,
                    'streamKey' => $response->streamKey,
                    'streamUrl' => 'rtmp://broadcast.api.video/s',
                    'broadcasting' => $response->broadcasting,
                    'hls_player' => $response->assets->hls,
                ),
                'statics' => array(
                    'likes' => $like_count,
                    'views' => $static->views,

                )
            ]);
        }
    }
}
