<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\Validator;
use Exception;
class QuizController extends Controller
{
   // this is my function to show all exam list
    public function student_course_exam_list(Request $request)
{
    $rollno = 'BSITF22M05'; // get this form session
    $results = DB::table('exam_papers')->get();

    $currentTime = Carbon::now('Asia/Karachi');

    foreach ($results as $exam) {
        $start = Carbon::parse($exam->start_time, 'Asia/Karachi');
        $end = Carbon::parse($exam->end_time, 'Asia/Karachi');

        if ($currentTime->lt($start)) {
            $exam->status = 'Upcoming';
        } elseif ($currentTime->gt($end)) {
            $exam->status = 'Ended';
        } else {
            $exam->status = 'Ongoing';
        }

        $exam->current_time = $currentTime->toDateTimeString();
        $exam->start_time_formatted = $start->toDayDateTimeString();
        $exam->end_time_formatted = $end->toDayDateTimeString();
    }
     return view('onlineexam.show_exam_list', compact('results','rollno'));    
}
  
// api get exam question and options
public function onlie_exam(Request $request)
{
    // ✅ exam_id & student_id dynamically le lo
    $exam_id    = $request->input('eid');
    $student_id = $request->input('sid');

    if (empty($exam_id) || empty($student_id)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Exam ID aur Student ID required hain.'
        ], 400);
    }

    // ✅ 1. Procedure call (Exam questions create hone ke liye)
    DB::statement("CALL exam_question_creation(?, ?)", [$exam_id, $student_id]);

    // ✅ 2. Exam Questions fetch karo
    $exam_questions_data = DB::select("
        SELECT 
            qb.id as question_id, 
            qb.qtitle as question, 
            qbd.options as option_text, 
            qbd.correct_option, 
            seqo.is_selected, 
            seqo.option_id,
            CASE WHEN seqo.is_selected = qbd.correct_option THEN 1 ELSE 0 END AS marks
        FROM students_exams_questions_options seqo
        LEFT JOIN question_bank_details qbd ON qbd.id = seqo.option_id
        LEFT JOIN question_bank qb ON qb.id = qbd.qid
        WHERE seqo.exam_id = ? AND seqo.student_id = ?
        ORDER BY qb.id
    ", [$exam_id, $student_id]);

    // ✅ 3. Exam details fetch karo
    $sql_exam_details = "SELECT * FROM exam_papers WHERE id = ?";
    $exam_details     = DB::select($sql_exam_details, [$exam_id]);

    if (empty($exam_details)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Exam not found for ID ' . $exam_id
        ], 404);
    }

    $exam = $exam_details[0];

    // ✅ 4. Time check
    $now   = Carbon::now('Asia/Karachi');
    $start = Carbon::parse($exam->start_time, 'Asia/Karachi');
    $end   = Carbon::parse($exam->end_time, 'Asia/Karachi');

    if ($now->greaterThan($end)) {
        $diffInSeconds = $now->diffInSeconds($end);
        $status = 'ended';
        $message = 'Exam ended ' . $this->formatDuration($diffInSeconds) . ' ago.';
    } elseif ($now->lessThan($start)) {
        $diffInSeconds = $start->diffInSeconds($now);
        $status = 'upcoming';
        $message = 'Exam will start in ' . $this->formatDuration($diffInSeconds) . '.';
    } else {
        $diffInSeconds = $end->diffInSeconds($now);
        $status = 'ongoing';
        $message = 'Exam is ongoing. Ends in ' . $this->formatDuration($diffInSeconds) . '.';
    }

    // ✅ 5. API JSON return karo
    return response()->json([
        'status' => 'success',
        'exam' => $exam,
        'exam_questions_data' => $exam_questions_data,
        'exam_status' => $status,
        'message' => $message,
        'start_time' => $start->toDateTimeString(),
        'end_time' => $end->toDateTimeString(),
        'current_time' => $now->toDateTimeString()
    ]);
}

private function formatDuration($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $remainingSeconds = $seconds % 60;

    $parts = [];

    if ($hours > 0) {
        $parts[] = "$hours hour" . ($hours > 1 ? 's' : '');
    }

    if ($minutes > 0) {
        $parts[] = "$minutes minute" . ($minutes > 1 ? 's' : '');
    }

    if ($remainingSeconds > 0 || empty($parts)) {
        $parts[] = "$remainingSeconds second" . ($remainingSeconds > 1 ? 's' : '');
    }

    return implode(', ', $parts);
}

  // this is my api function for exam result
   public function exam_result(Request $request)
{
    $examId = $request->input('eid');
    $studentId = $request->input('sid');

    if (!$examId || !$studentId) {
        return response()->json([
            'success' => false,
            'message' => 'exam_id and student_id are required'
        ], 400);
    }
    $results = DB::select("
        SELECT 
            qb.id as question_id, 
            qb.qtitle as question, 
            qbd.options as option_text, 
            qbd.correct_option, 
            seqo.is_selected, 
            seqo.option_id,
            CASE WHEN seqo.is_selected = qbd.correct_option THEN 1 ELSE 0 END AS marks
        FROM students_exams_questions_options seqo
        LEFT JOIN question_bank_details qbd ON qbd.id = seqo.option_id
        LEFT JOIN question_bank qb ON qb.id = qbd.qid
        WHERE seqo.exam_id = ? AND seqo.student_id = ?
        ORDER BY qb.id
    ", [$examId, $studentId]);

    return response()->json([
        'success' => true,
        'exam_id' => $examId,
        'student_id' => $studentId,
        'data' => $results
    ]);
}
// api that update my options of the questions 1 for select 0 for unselect 
public function update_option(Request $request)
{
    $questionId = $request->input('question_id');
    $optionId   = $request->input('option_id');

    // Validation
    $validator = Validator::make([
        'question_id' => $questionId,
        'option_id'   => $optionId,
    ], [
        'question_id' => 'required|integer',
        'option_id'   => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error' => $validator->errors()
        ], 422);
    }

    // Fetch current option
    $option = DB::table('students_exams_questions_options')
        ->where('question_id', $questionId)
        ->where('option_id', $optionId)
        ->first();

    if (!$option) {
        return response()->json([
            'error' => 'Option not found for this question'
        ], 404);
    }

    // Toggle is_selected
    $newStatus = $option->is_selected == 1 ? 0 : 1;

    DB::table('students_exams_questions_options')
        ->where('question_id', $questionId)
        ->where('option_id', $optionId)
        ->update(['is_selected' => $newStatus]);

    return response()->json([
        'message'     => 'Option updated successfully',
        'question_id' => $questionId,
        'option_id'   => $optionId,
        'is_selected' => $newStatus
    ]);
}

}













    