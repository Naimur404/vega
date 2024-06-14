<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisualizationController;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [VisualizationController::class, 'index']);
Route::get('/data/{id}', [VisualizationController::class, 'getData']);
Route::post('/save-rating', [VisualizationController::class, 'saveRating']);
Route::get('/export-csv', [VisualizationController::class, 'exportCsv'])->name('ratings.export.csv');
