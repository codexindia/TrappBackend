<?php

use Carbon\CarbonInterval;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Audio\Aac;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Route;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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
Route::get('/get', function () {
    $lowBitrate = (new X264)->setKiloBitrate(250);
$midBitrate = (new X264)->setKiloBitrate(500);
$highBitrate = (new X264)->setKiloBitrate(1000);
    $media = FFMpeg::open('demo.mp4')
    ->exportForHLS()
   
    ->addFormat($lowBitrate)
   
    ->save('adaptive_steve.m3u8');

     
});
