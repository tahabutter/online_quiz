<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Student Dashboard & Exam List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- TailwindCSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background-color: #f0f4f8;
      color: #1f2937;
      margin: 0;
    }

    .status-badge {
      @apply inline-block text-xs font-semibold px-3 py-1 rounded-full;
    }
    .status-ended {
      background-color: #fee2e2; 
      color: #b91c1c;           
    }
    .status-ongoing {
      background-color: #d1fae5;
      color: #065f46;          
    }
    .status-upcoming {
      background-color: #fef3c7; 
      color: #92400e;          
    }
  </style>
</head>

<body class="min-h-screen">
  <header class="max-w-6xl mx-auto px-4 pt-8">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-center md:text-left">Student Course Exam List</h1>

      <!-- Create Question button -->
      <div class="flex items-center space-x-2">
        <a href="{{ ('student.create.question') }}" class="inline-block">
          <button
            type="button"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
            aria-label="Create Question"
          >
            Create Question
          </button>
        </a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-8">
    <section class="bg-white rounded-xl shadow-md overflow-hidden">
      <!-- table container -->
      <div id="examTable" class="w-full overflow-x-auto p-4">
        <table class="min-w-full table-auto">
          <thead>
            <tr class="bg-blue-600 text-white text-left">
              <th class="px-4 py-3 w-1/4">Exam & Course</th>
              <th class="px-4 py-3 w-1/6">Status</th>
              <th class="px-4 py-3 w-1/2">Time</th>
              <th class="px-4 py-3 w-1/6">Action</th>
            </tr>
          </thead>

          <tbody class="text-sm divide-y">
            @forelse($results as $result)
              <tr class="hover:bg-gray-50">
                <!-- Exam & Course -->
                <td class="px-4 py-4 align-top">
                  <div class="font-semibold text-gray-800">{{ $result->exam_name }}</div>
                  <div class="text-gray-500 text-xs mt-1">{{ $result->course_offer_id }}</div>
                </td>

                <!-- Status -->
                <td class="px-4 py-4 align-top">
                  @php
                    $statusClass = match($result->status) {
                      'Ended' => 'status-ended',
                      'Ongoing' => 'status-ongoing',
                      default => 'status-upcoming'
                    };
                  @endphp
                  <span class="status-badge {{ $statusClass }}" aria-live="polite">
                    {{ $result->status }}
                  </span>
                </td>

                <!-- Time -->
                <td class="px-4 py-4 align-top">
                  <div class="text-xs text-gray-600">
                    <div>
                      <span class="font-medium">Start:</span>
                      <span>{{ \Carbon\Carbon::parse($result->start_time)->format('h:i A - M d, Y') }}</span>
                    </div>
                    <div class="mt-1">
                      <span class="font-medium">End:</span>
                      <span>{{ \Carbon\Carbon::parse($result->end_time)->format('h:i A - M d, Y') }}</span>
                    </div>
                  </div>
                </td>

                <!-- Action -->
                <td class="px-4 py-4 align-top">
                  <div class="flex items-center gap-2">
                    @if($result->status === 'Ended')
                      <button
                        class="px-3 py-1 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        data-action="view-result"
                        data-exam-id="{{ $result->id }}"
                        aria-label="View result for {{ $result->exam_name }}"
                      >
                        View Result
                      </button>
                    @elseif($result->status === 'Upcoming')
                      <button
                        class="px-3 py-1 rounded-md bg-gray-300 text-gray-700 text-sm cursor-not-allowed"
                        disabled
                        aria-disabled="true"
                      >
                        Start Exam
                      </button>
                    @elseif($result->status === 'Ongoing')
                      <button
                        class="px-3 py-1 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-green-300"
                        data-action="start-exam"
                        data-exam-id="{{ $result->id }}"
                        aria-label="Start exam {{ $result->exam_name }}"
                      >
                        Start Exam
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                  No exams available.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    (function () {
      const studentEmail = @json(session('user')['email'] ?? '');

      const buildResultUrl = (examId, sid) => `/result?eid=${encodeURIComponent(examId)}&sid=${encodeURIComponent(sid)}`;
      const buildShowQuizUrl = (examId, sid) => `/showquiz?eid=${encodeURIComponent(examId)}&sid=${encodeURIComponent(sid)}`;

      document.getElementById('examTable').addEventListener('click', function (ev) {
        const btn = ev.target.closest('button[data-action]');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        const examId = btn.getAttribute('data-exam-id');
        if (!examId) return;

        if (action === 'view-result') {
          window.location.href = buildResultUrl(examId, studentEmail);
        } else if (action === 'start-exam') {
          window.location.href = buildShowQuizUrl(examId, studentEmail);
        }
      });

      async function refreshExamTable() {
        try {
          const res = await fetch(window.location.href, { credentials: 'same-origin' });
          if (!res.ok) throw new Error('Network error');
          const text = await res.text();
          const parser = new DOMParser();
          const doc = parser.parseFromString(text, 'text/html');
          const newTable = doc.getElementById('examTable');
          if (newTable) {
            document.getElementById('examTable').innerHTML = newTable.innerHTML;
          }
        } catch (err) {
          console.error('Error refreshing exam table:', err);
        }
      }

      setInterval(refreshExamTable, 10000);
      document.addEventListener('visibilitychange', function () {
        if (!document.hidden) refreshExamTable();
      });
      window.addEventListener('beforeunload', function () {
        clearInterval(refreshExamTable);
      });
    })();
  </script>
</body>
</html>
