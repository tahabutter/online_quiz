<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz Results</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

  <style>
    :root {
      --primary-color: #4361ee;
      --success-color: #4cc9f0;
      --warning-color: #f72585;
      --dark-color: #212529;
      --light-color: #f8f9fa;
    }

    body {
      background-color: #f5f7fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Cards */
    .result-card {
      margin-bottom: 30px;
      border-radius: 15px;
      border: none;
      background: white;
      overflow: hidden;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    .result-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 20px rgba(0,0,0,0.15);
    }

    /* Question */
    .question-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      position: relative;
    }
    .question-title:after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 50px; height: 3px;
      border-radius: 3px;
      background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    }

    /* Options */
    .option-row {
      padding: 0.75rem 1.25rem;
      border-radius: 8px;
      margin-bottom: .5rem;
      transition: all 0.3s ease;
      position: relative;
    }
    .option-row:hover { transform: translateX(5px); }

    .correct-option {
      background: rgba(76,201,240,.2);
      border-left: 4px solid #4cc9f0;
    }
    .user-correct {
      background: rgba(100,200,150,.2);
      border-left: 4px solid #28a745;
    }
    .user-wrong {
      background: rgba(247,37,133,.1);
      border-left: 4px solid #f72585;
    }

    /* Dashboard Cards */
    .dashboard-card {
      border: none;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      margin-bottom: 20px;
      color: white;
      position: relative;
      z-index: 1;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    }
    .dashboard-card::before {
      content: '';
      position: absolute;
      top:0; left:0;
      width:100%; height:100%;
      background: linear-gradient(135deg, rgba(255,255,255,.2), rgba(255,255,255,0));
      z-index:-1;
    }
    .card-blue { background: linear-gradient(135deg,#4361ee,#3a0ca3); }
    .card-green { background: linear-gradient(135deg,#4cc9f0,#4895ef); }
    .card-orange { background: linear-gradient(135deg,#f72585,#b5179e); }

    /* Circular Progress */
    .circular-progress {
      position: relative;
      width: 100px; height: 100px;
      margin: 0 auto 15px;
    }
    .circular-progress svg { width: 100%; height: 100%; }
    .circular-progress circle {
      fill: none; stroke-width: 8; stroke-linecap: round;
      transform: rotate(-90deg); transform-origin: 50% 50%;
    }
    .circular-progress .bg { stroke: rgba(255,255,255,.2); }
    .circular-progress .progress {
      stroke: white; stroke-dasharray: 314; stroke-dashoffset: 314;
      animation: circle-fill 1.5s ease-in-out forwards;
    }
    @keyframes circle-fill { to { stroke-dashoffset: var(--dash-offset); } }
    .circular-progress .percentage {
      position: absolute; top:50%; left:50%;
      transform: translate(-50%,-50%);
      font-size: 1.2rem; font-weight: bold; color: white;
    }

    /* Animations */
    .header-animation {
      background: linear-gradient(90deg,#4361ee,#3a0ca3,#7209b7,#f72585);
      background-size: 300% 300%;
      animation: gradient 8s ease infinite;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    @keyframes gradient {
      0%{background-position:0% 50%} 
      50%{background-position:100% 50%} 
      100%{background-position:0% 50%}
    }
    .floating { animation: floating 3s ease-in-out infinite; }
    @keyframes floating { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

    /* Alerts */
    .alert-container {
      position: fixed; top: 20px; right: 20px;
      z-index: 9999; max-width: 350px;
      width: 90%;
    }

    /* Responsive */
    @media (max-width:768px) {
      .circular-progress { width: 80px; height: 80px; }
      .circular-progress .percentage { font-size: 1rem; }
      .question-title { font-size: 1rem; }
      .badge { font-size: .8rem; }
    }
    @media (max-width:576px) {
      .option-row { flex-direction: column; }
      .option-row .col-md-4 { margin-top: 5px; text-align:left !important; }
      .alert-container { width:95%; right:10px; left:10px; margin:0 auto; }
    }
  </style>
</head>
<body>

<div class="container py-3 py-md-5">
  <div class="alert-container" id="alert-container"></div>

  <h2 class="mb-4 text-center display-4 fw-bold header-animation animate__animated animate__fadeInDown">Results</h2>

  <div class="row text-white mb-4 g-3 animate__animated animate__fadeIn" id="dashboard-cards"></div>
  <div id="results-container" class="animate__animated animate__fadeIn">
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Loading your results...</p>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){

  function getParams(){
    const urlParams = new URLSearchParams(window.location.search);
    return { eid: urlParams.get('eid'), sid: urlParams.get('sid') };
  }

  function showAlert(type, msg){
    const html = `
      <div class="alert alert-${type} alert-dismissible fade show animate__animated animate__fadeInRight" role="alert">
        ${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;
    $('#alert-container').append(html);
    setTimeout(()=>{$('.alert').fadeOut(()=>$(this).remove());},5000);
  }

  function handleError(err){
    console.error('API Error:',err);
    let msg = 'Error fetching results.';
    if(err.status===404) msg='API endpoint not found.';
    else if(err.status===500) msg='Server error, try again later.';
    else if(err.responseJSON?.message) msg=err.responseJSON.message;

    showAlert('danger', `<i class="bi bi-exclamation-triangle-fill"></i> ${msg}`);
    $('#results-container').html(`<div class="alert alert-danger"><i class="bi bi-x-circle-fill"></i> ${msg}</div>`);
  }

  function groupBy(arr,key){
    return arr.reduce((res,item)=>((res[item[key]]=res[item[key]]||[]).push(item),res),{});
  }

  function renderResults(data){
    if(!data.length) return $('#results-container').html(`<div class="alert alert-info"><i class="bi bi-info-circle-fill"></i> No results found.</div>`);

    if(!data[0].hasOwnProperty('question_id')) 
      return $('#results-container').html(`<div class="alert alert-danger"><i class="bi bi-x-circle-fill"></i> Invalid data format.</div>`);

    const grouped = groupBy(data,'question_id');
    let total=0, obtained=0, html='';

    for(const qid in grouped){
      const opts=grouped[qid];
      const title=opts[0]?.qtitle||opts[0]?.question||`Question ${qid}`;
      const correct=opts.filter(o=>o.correct_option==1);
      const selectedCorrect=opts.filter(o=>o.is_selected==1&&o.correct_option==1);
      const selectedWrong=opts.filter(o=>o.is_selected==1&&o.correct_option==0);

      total+=correct.length; obtained+=selectedCorrect.length;

      html+=`<div class="card result-card animate__animated animate__fadeInUp">
        <div class="card-body">
          <p class="question-title">Q: ${title}</p>
          <div class="mb-3">
            <span class="badge bg-primary">Total Marks: ${correct.length}</span>
            <span class="badge bg-success">Obtained: ${selectedCorrect.length}</span>
          </div>`;

      opts.forEach(o=>{
        let cls='', indicator='';
        const txt=o.options||o.option_text||`Option ${o.option_id}`;
        if(o.is_selected==1){
          if(o.correct_option==1){ cls='user-correct'; indicator='<span class="float-end text-success"><i class="bi bi-check-circle-fill"></i> Your Answer (Correct)</span>'; }
          else { cls='user-wrong'; indicator='<span class="float-end text-danger"><i class="bi bi-x-circle-fill"></i> Your Answer (Wrong)</span>'; }
        } else if(o.correct_option==1){
          cls='correct-option'; indicator='<span class="float-end text-info"><i class="bi bi-check-circle-fill"></i> Correct Answer</span>';
        }
        html+=`<div class="row option-row ${cls}">
          <div class="col-md-8">${txt}</div>
          <div class="col-md-4 text-end">${indicator}</div>
        </div>`;
      });
      html+='</div></div>';
    }

    const perc=total?(obtained/total*100).toFixed(2):0;
    const dashOffset=314*(1-(perc/100));

    $('#dashboard-cards').html(`
      <div class="col-md-4"><div class="dashboard-card card-blue floating">
        <h5>Total Marks</h5>
        <div class="circular-progress">
          <svg viewBox="0 0 100 100">
            <circle class="bg" cx="50" cy="50" r="45"></circle>
            <circle class="progress" cx="50" cy="50" r="45" style="--dash-offset:${total?0:314}"></circle>
          </svg><div class="percentage">${total}</div>
        </div></div></div>

      <div class="col-md-4"><div class="dashboard-card card-green floating">
        <h5>Obtained</h5>
        <div class="circular-progress">
          <svg viewBox="0 0 100 100">
            <circle class="bg" cx="50" cy="50" r="45"></circle>
            <circle class="progress" cx="50" cy="50" r="45" style="--dash-offset:${314*(1-(obtained/total||0))}"></circle>
          </svg><div class="percentage">${obtained}</div>
        </div></div></div>

      <div class="col-md-4"><div class="dashboard-card card-orange floating">
        <h5>Percentage</h5>
        <div class="circular-progress">
          <svg viewBox="0 0 100 100">
            <circle class="bg" cx="50" cy="50" r="45"></circle>
            <circle class="progress" cx="50" cy="50" r="45" style="--dash-offset:${dashOffset}"></circle>
          </svg><div class="percentage">${perc}%</div>
        </div></div></div>`);

    $('#results-container').html(html);
  }

  function loadResults(){
    const {eid,sid}=getParams();
    if(!eid||!sid) 
      return $('#results-container').html(`<div class="alert alert-danger"><i class="bi bi-x-circle-fill"></i> Missing parameters.</div>`);

    $.ajax({
      url:`http://127.0.0.1:8000/api/check_exam_result?eid=${eid}&sid=${sid}`,
      method:'GET',
      beforeSend:()=>$('#results-container').html(`<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>`),
      success:r=>r.success&&Array.isArray(r.data)?renderResults(r.data):$('#results-container').html(`<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill"></i> ${r.message||'No result data found.'}</div>`),
      error:handleError
    });
  }

  setTimeout(loadResults,1500);
});
</script>
</body>
</html>
