<?php

use Carbon\CarbonInterval;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Audio\Aac;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Route;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Storage;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/get', function () {
//     return Storage::disk('digitalocean')->get('Y2meta.app-Bem-vindos ao meu mundo_ O início da jornada no canal do Léo Silva-(720p).mp4');
//    // Storage::disk('digitalocean')->put('filename.txt', 'asas');
//     return  Storage::disk('digitalocean')->append('big_buck_bunny_720p_2mb.mp4',file_get_contents(Storage::disk('digitalocean')->get('Y2meta.app-Bem-vindos ao meu mundo_ O início da jornada no canal do Léo Silva-(720p).mp4')));
//     function renameFile($oldPath, $newPath)
//     {
//         $disk = Storage::disk('digitalocean'); // Assuming 'spaces' is your configured disk name

//         if ($disk->exists($oldPath)) {
//             // Copy the file to the new path
//            return $disk->append($oldPath, $newPath);

//             // Delete the old file
//             $disk->delete($oldPath);

//             return true;
//         }

//         return false;
//     }
//    return dd(renameFile('filename.txt','abcd.txt'));
// });
