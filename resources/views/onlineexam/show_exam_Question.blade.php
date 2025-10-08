<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Online Examination Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --light: #f8f9fa;
      --dark: #212529;
      --white: #ffffff;
      --gray: #6c757d;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      color: #495057;
      line-height: 1.6;
      padding-top: 120px;
    }
    .exam-header { 
      background: linear-gradient(135deg, #28a745 0%, #218838 100%); 
      color: white; 
      padding: 1rem 0; 
      box-shadow: 0 4px 20px rgba(40, 167, 69, 0.15); 
      position: fixed; 
      top: 0; left: 0; right: 0; 
      z-index: 1030; 
      transition: all 0.3s ease; 
    }
    .exam-header.warning { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); }
    .exam-header.ended { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
    .timer-display { 
      font-family: 'Montserrat', sans-serif; 
      font-weight: 600; 
      font-size: 1.8rem; 
      letter-spacing: 1px; 
      color: var(--white); 
      transition: color 0.3s ease; 
    }
    .timer-display.red { color: #ff4d4f !important; }
    .timer-label { font-size: 0.85rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 1px; }
    .status-badge { padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 500; margin: 0 0.5rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .progress-container { height: 8px; background-color: #e9ecef; border-radius: 4px; margin: 1.5rem auto; overflow: hidden; }
    .progress-bar { background: linear-gradient(90deg,rgb(40, 112, 167), #17a2b8); transition: width 0.4s ease; height: 100%; }
    .questions-container { max-width: 800px; margin: 0 auto; padding: 2rem 1rem; }
    .section-title { 
      font-family: 'Montserrat', sans-serif; 
      font-weight: 600; 
      color: var(--primary); 
      margin-bottom: 2rem; 
      position: relative; 
      display: inline-block; 
      padding-bottom: 10px;
    }
    .section-title::after { 
      content: ''; 
      position: absolute; 
      bottom: 0; 
      left: 0; 
      width: 100%; 
      height: 3px; 
      background: linear-gradient(90deg, var(--primary), transparent); 
      border-radius: 3px; 
    }
    .question-card { 
      background: var(--white); 
      border-radius: 12px; 
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); 
      margin-bottom: 2rem; 
      border-left: 4px solid var(--primary); 
      transition: all 0.3s ease; 
      overflow: hidden; 
    }
    .question-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); }
    .question-title { 
      font-weight: 600; 
      color: var(--dark); 
      margin-bottom: 1.5rem; 
      position: relative; 
      padding-left: 2.5rem; 
      font-size: 1.2rem; 
      line-height: 1.5;
    }
    .question-title::before { 
      content: "Q"; 
      position: absolute; 
      left: 0; 
      top: 0;
      height: 28px; 
      width: 28px; 
      background: var(--primary); 
      border-radius: 50%; 
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: bold;
    }
    .option-item { 
      display: block; 
      padding: 1.25rem 1.5rem 1.25rem 4rem; 
      margin-bottom: 0.75rem; 
      background: var(--light); 
      border-radius: 8px; 
      border: 1px solid #e9ecef; 
      cursor: pointer; 
      transition: all 0.2s ease; 
      position: relative; 
    }
    .option-item:hover { 
      background: #e9f5ff; 
      border-color: #cce5ff; 
      transform: translateX(5px);
    }
    .option-item input[type="checkbox"] { 
      position: absolute; 
      opacity: 0; 
      cursor: pointer; 
    }
    .custom-checkbox { 
      position: absolute; 
      top: 50%; 
      left: 1.5rem; 
      transform: translateY(-50%);
      height: 1.5rem; 
      width: 1.5rem; 
      background-color: white; 
      border: 2px solid #adb5bd; 
      border-radius: 50%; 
      transition: all 0.2s ease; 
    }
    .option-item input:checked ~ .custom-checkbox { 
      background-color: var(--primary); 
      border-color: var(--primary); 
    }
    .custom-checkbox::after { 
      content: ""; 
      position: absolute; 
      display: none; 
      left: 5px; 
      top: 1px; 
      width: 5px; 
      height: 10px; 
      border: solid white; 
      border-width: 0 2px 2px 0; 
      transform: rotate(45deg); 
    }
    .option-item input:checked ~ .custom-checkbox::after { display: block; }
    .option-text { 
      display: block; 
      font-weight: 500;
    }
    .option-letter {
      display: inline-block;
      font-weight: 700;
      color: var(--primary);
      margin-right: 8px;
      min-width: 24px;
    }
    .spinner { 
      width: 16px; 
      height: 16px; 
      border: 3px solid rgba(0, 0, 0, 0.1); 
      border-top-color: var(--primary); 
      border-radius: 50%; 
      animation: spin 0.8s linear infinite; 
      display: inline-block; 
      margin-left: 8px; 
      vertical-align: middle; 
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .exam-ended-wrapper { margin-top: 20px; }
    .exam-ended-card { 
      max-width: 800px; 
      margin: 2rem auto; 
      border: none; 
      border-radius: 15px; 
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); 
      overflow: hidden; 
      text-align: center; 
      background: white; 
      padding: 3rem 2rem; 
    }
    .exam-ended-icon { 
      font-size: 4rem; 
      color: var(--success); 
      margin-bottom: 1.5rem; 
    }
    .results-btn { 
      padding: 0.75rem 2rem; 
      border-radius: 50px; 
      font-weight: 500; 
      transition: all 0.3s ease; 
      background: linear-gradient(135deg, #dc3545, #c82333); 
      border: none; 
      color: white; 
      box-shadow: 0 4px 15px rgba(171, 13, 13, 0.78); 
    }
    .results-btn:hover { 
      transform: translateY(-2px); 
      box-shadow: 0 6px 20px rgba(235, 15, 15, 0.87); 
      color: white; 
    }
    
    /* New styles for enhanced UI */
    .question-number {
      font-size: 0.9rem;
      color: var(--gray);
      margin-bottom: 5px;
    }
    .option-highlight {
      font-weight: 700;
      color: var(--primary);
    }
    .navigation-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 2rem;
    }
    .nav-btn {
      padding: 0.6rem 1.5rem;
      border-radius: 6px;
      font-weight: 500;
      background: var(--primary);
      color: white;
      border: none;
      transition: all 0.3s ease;
    }
    .nav-btn:hover {
      background: var(--secondary);
      transform: translateY(-2px);
    }
    .nav-btn:disabled {
      background: var(--gray);
      cursor: not-allowed;
      transform: none;
    }
    
    /* Pagination styles */
    .pagination-container {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
      flex-wrap: wrap;
    }
    .page-indicator {
      display: flex;
      align-items: center;
      margin: 0 1rem;
      font-weight: 500;
    }
    .page-btn {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      background: var(--light);
      border: 1px solid #dee2e6;
      margin: 0 0.25rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .page-btn:hover {
      background: var(--primary);
      color: white;
    }
    .page-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }
    .page-btn-nav {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      background: var(--primary);
      color: white;
      border: none;
      font-weight: 500;
      margin: 0 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .page-btn-nav:hover {
      background: var(--secondary);
      transform: translateY(-2px);
    }
    .page-btn-nav:disabled {
      background: var(--gray);
      cursor: not-allowed;
      transform: none;
    }
    
    @media (max-width: 768px) {
      body {
        padding-top: 140px;
      }
      .exam-header {
        padding: 0.8rem 0;
      }
      .timer-display {
        font-size: 1.5rem;
      }
      .status-badge {
        margin: 0.25rem;
        padding: 0.4rem 0.8rem;
      }
      .question-card {
        padding: 1.5rem;
      }
      .option-item {
        padding: 1rem 1rem 1rem 3.5rem;
      }
      .custom-checkbox {
        left: 1rem;
      }
      .pagination-container {
        flex-direction: column;
        align-items: center;
      }
      .page-indicator {
        margin: 1rem 0;
      }
    }
  </style>
</head>

<body>

@php
  $examId = request()->query('eid');
  $studentId = request()->query('sid');
@endphp

<header class="exam-header" id="examHeader">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <div class="timer-display" id="timerDisplay">
          <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
        </div>
        <div class="timer-label">Time Remaining</div>
      </div>
      <div class="col-md-6">
        <div class="d-flex flex-wrap justify-content-center justify-content-md-end">
          <span class="status-badge bg-primary"><i class="fas fa-clipboard-list me-1"></i> Total: <span id="total">0</span></span>
          <span class="status-badge bg-success"><i class="fas fa-check-circle me-1"></i> Attempted: <span id="attempted">0</span></span>
          <span class="status-badge bg-danger"><i class="fas fa-times-circle me-1"></i> Missed: <span id="missed">0</span></span>
        </div>
      </div>
    </div>
    <div class="progress-container"><div class="progress-bar" id="progressBar"></div></div>
  </div>
</header>

<main class="container py-4" id="mainContent">
  <section class="questions-container">
    <h2 class="section-title"><i class="fas fa-question-circle me-2"></i>Exam Questions</h2>
    <div id="questionsArea"></div>
    
    <div class="pagination-container" id="paginationContainer">
      <button class="page-btn-nav" id="prevPageBtn" disabled><i class="fas fa-chevron-left me-1"></i> Previous</button>
      <div class="page-indicator">Page <span id="currentPage">1</span> of <span id="totalPages">1</span></div>
      <div id="pageNumbers" class="d-flex flex-wrap justify-content-center"></div>
      <button class="page-btn-nav" id="nextPageBtn">Next <i class="fas fa-chevron-right ms-1"></i></button>
    </div>
  </section>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const eid = "{{ $examId }}";
const sid = "{{ $studentId }}";
const apiUrl = `api/online_exam?eid=${eid}&sid=${sid}`;
const updateUrl = `api/update_options`;

let questionMap = new Map();
let timerInterval;
let examEnded = false;
let currentPage = 1;
const questionsPerPage = 5;
let totalPages = 1;
let questionCards = [];

// Load Questions
$.getJSON(apiUrl, function(data) {
  let serverNow = new Date(data.current_time);
  const end = new Date(data.end_time);
  const examHeader = $('#examHeader');
  const timerDisplay = $('#timerDisplay');

  function updateCountdown() {
    const diff = end - serverNow;
    if (diff <= 0) {
      examEnded = true;
      clearInterval(timerInterval);
      examHeader.hide();
      $('#mainContent').html(`
        <div class="exam-ended-wrapper">
          <div class="exam-ended-card">
            <div class="exam-ended-icon"><i class="fas fa-check-circle"></i></div>
            <h2 class="card-title mb-3">Exam Submitted Successfully</h2>
            <p class="card-text text-muted mb-4">Your answers have been automatically submitted. You can now view your results.</p>
            <a href="/result?eid=${eid}&sid=${sid}" class="btn results-btn"><i class="fas fa-chart-bar me-2"></i> View Result</a>
          </div>
        </div>`);
      return;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    $('#hours').text(String(hours).padStart(2,"0"));
    $('#minutes').text(String(minutes).padStart(2,"0"));
    $('#seconds').text(String(seconds).padStart(2,"0"));

    if (diff <= 60000) {
      examHeader.addClass("warning");
      timerDisplay.addClass("red");
    } else {
      examHeader.removeClass("warning");
      timerDisplay.removeClass("red");
    }

    serverNow = new Date(serverNow.getTime() + 1000);
  }

  updateCountdown();
  timerInterval = setInterval(updateCountdown, 1000);

  // Group questions
  data.exam_questions_data.forEach(item => {
    if (!questionMap.has(item.question_id)) questionMap.set(item.question_id, []);
    questionMap.get(item.question_id).push(item);
  });

  // Render questions
  const container = $('#questionsArea');
  questionMap.forEach((options, qid) => {
    const questionCard = $('<div>', {class:'question-card p-4 mb-4', 'data-question-id': qid});
    let html = `<h3 class="question-title">${options[0].question}</h3>`;
    options.forEach((opt,index)=>{
      const checked = opt.is_selected==1 ? "checked":"";
      const chkId = `chk_${opt.option_id}`;
      const spinnerId = `spinner_${opt.option_id}`;
      const optionLetter = String.fromCharCode(65+index);
      html+=`
        <label class="option-item" for="${chkId}">
          <input type="checkbox" id="${chkId}" ${checked}>
          <span class="custom-checkbox"></span>
          <span class="option-text">
            <span class="option-letter">${optionLetter}.</span> ${opt.option_text}
            <span id="${spinnerId}" class="spinner" style="display:none;"></span>
          </span>
        </label>`;
    });
    questionCard.html(html);
    container.append(questionCard);
    questionCards.push(questionCard);

    // Checkbox change event with AJAX
    options.forEach(opt=>{
      $(`#chk_${opt.option_id}`).change(function(){
        const checkbox = $(this);
        const spinner = $(`#spinner_${opt.option_id}`);
        checkbox.prop('disabled',true);
        spinner.show();

        $.ajax({
          url:updateUrl,
          method:'POST',
          data:{option_id:opt.option_id, question_id:opt.question_id, eid:eid, sid:sid},
          success:function(res){
            if(res.error) { console.error(res.error); return; }
            // update questionMap
            options.forEach(o=>{ if(o.option_id==opt.option_id) o.is_selected=res.is_selected; });
            // refresh checkboxes
            options.forEach(o=>{
              $(`#chk_${o.option_id}`).prop('checked', o.is_selected==1);
            });
            updateQuestionCounters();
          },
          error:function(err){ console.error(err); },
          complete:function(){ spinner.hide(); checkbox.prop('disabled',false); }
        });
      });
    });
  });

  totalPages = Math.ceil(questionCards.length / questionsPerPage);
  setupPagination();
  showPage(1);
  updateQuestionCounters();
});

// Update Counters
function updateQuestionCounters(){
  const total = questionMap.size;
  let attempted = 0;
  questionMap.forEach(options=>{ if(options.some(o=>o.is_selected==1)) attempted++; });
  $('#total').text(total);
  $('#attempted').text(attempted);
  $('#missed').text(total-attempted);
  $('#progressBar').css('width', (attempted/total*100)+'%');
}

// Pagination
function setupPagination(){
  const pageNumbersContainer = $('#pageNumbers');
  $('#totalPages').text(totalPages);
  for(let i=1;i<=totalPages;i++){
    const btn = $('<button>',{class:'page-btn',text:i});
    btn.click(()=>showPage(i));
    pageNumbersContainer.append(btn);
  }
  $('#prevPageBtn').click(()=>{ if(currentPage>1) showPage(currentPage-1); });
  $('#nextPageBtn').click(()=>{ if(currentPage<totalPages) showPage(currentPage+1); });
}

function showPage(pageNum){
  currentPage = pageNum;
  questionCards.forEach(card=>card.hide());
  const startIndex = (pageNum-1)*questionsPerPage;
  const endIndex = Math.min(startIndex+questionsPerPage, questionCards.length);
  for(let i=startIndex;i<endIndex;i++) questionCards[i].show();
  $('#currentPage').text(pageNum);
  $('.page-btn').each((i,btn)=>$(btn).toggleClass('active', i+1===pageNum));
  $('#prevPageBtn').prop('disabled', pageNum===1);
  $('#nextPageBtn').prop('disabled', pageNum===totalPages);
}
</script>
</body>
</html>