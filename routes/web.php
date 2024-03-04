<?php

use Carbon\CarbonInterval;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Audio\Aac;
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
    $media = FFMpeg::open('demo.mkv');
    $durationInSeconds = $media->getDurationInSeconds(); // returns an int
    return CarbonInterval::seconds($durationInSeconds)->cascade()->forHumans()  ?? '';
     
});
