<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student_QuizController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 // Index page with exam papers
Route::get('/student_course_list', [Student_QuizController::class, 'student_course_exam_list'])->name('table_quiz');
// this is my that check result 
Route::get('/result', function () {
    return view('onlineexam.show_exam_result');
    })->name('onlineexam.show_exam_result');

// Show Quiz Route
Route::get('/showquiz', function () {
    return view('onlineexam.show_exam_Question'); 
})->name('onlineexam.show_exam_Question');

Route::get('/', function () {
    return view('welcome');
});

