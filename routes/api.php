<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student_QuizController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// this is route for api to check exam result 
Route::get('/check_exam_result', [Student_QuizController::class, 'exam_result']);    
// api call of get question and options
Route::get('/online_exam', [Student_QuizController::class, 'online_exam'])->name('online_exam');
// api that update my options of the questions 1 for select 0 for unselect
Route::post('/update_options', [Student_QuizController::class, 'update_option']);