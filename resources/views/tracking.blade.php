<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/complaint-system.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            background: linear-gradient(135deg, #f4f7fc 0%, #e0e7ff 100%);
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
        }

        .simple-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15), 0 2px 8px #e0e7ff;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 420px;
            margin: 2.5rem auto;
            backdrop-filter: blur(2px);
            border: 1px solid #e0e7ff;
            transition: box-shadow 0.3s;
        }

        .simple-card:hover {
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.22), 0 4px 16px #c7d2fe;
        }

        .simple-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, #6366f1 0%, #1b1b18 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .simple-card label {
            font-size: 1.05rem;
            margin-bottom: 0.4rem;
            display: block;
            color: #6366f1;
            font-weight: 500;
        }

        .simple-card input {
            width: 100%;
            border: 1.5px solid #c7d2fe;
            border-radius: 0.75rem;
            padding: 0.7rem 1.1rem;
            margin-bottom: 1.2rem;
            font-size: 1.08rem;
            background: #f8fafc;
            transition: border 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 2px #e0e7ff;
        }

        .simple-card input:focus {
            outline: none;
            border: 2px solid #6366f1;
            box-shadow: 0 0 0 3px #6366f133;
            background: #fff;
        }

        .simple-card button {
            background: linear-gradient(90deg, #6366f1 0%, #1b1b18 100%);
            color: #fff;
            border: none;
            border-radius: 0.75rem;
            padding: 0.7rem 1.5rem;
            font-size: 1.08rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px #e0e7ff;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: 0.03em;
        }

        .simple-card button:hover {
            background: linear-gradient(90deg, #1b1b18 0%, #6366f1 100%);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 6px 20px #c7d2fe;
        }

        .simple-card .text-red-600 {
            color: #dc3545;
            margin-top: 1rem;
            font-weight: 500;
            background: #fff0f3;
            border-radius: 0.5rem;
            padding: 0.7rem 1rem;
            box-shadow: 0 1px 4px #fca5a5;
        }
    </style>
</head>

<body>
    <div id="app">
        <main class="py-4">
            <div class="container py-4 flex flex-col items-center gap-8">
                <div class="w-full lg:max-w-2xl max-w-[335px]">
                    <div class="simple-card">
                        <h2 class="text-xl font-semibold mb-4">Track Your Complaint</h2>
                        <form method="GET" action="{{ route('tracking.get') }}" class="space-y-4">
                            <div>
                                <label for="tracking_id" class="block text-sm font-medium mb-1">Tracking ID</label>
                                <input type="text" id="tracking_id" name="tracking_id"
                                    class="w-full border border-[#e3e3e0] rounded-sm px-3 py-2" required
                                    value="{{ request('tracking_id') }}">
                            </div>
                            <button type="submit"
                                class="bg-[#1b1b18] text-white px-4 py-2 rounded-sm hover:bg-black transition">Track</button>
                        </form>
                        @if (!empty($error))
                            <div class="mt-3 text-red-600">
                                {{ $error }}
                            </div>
                        @endif
                    </div>
                </div>
                @if (!empty($complaint))
                    <div class="w-full lg:max-w-3xl max-w-[98vw]">
                        <div class="simple-card" style="margin-top:0; max-width:900px; margin-left:auto; margin-right:auto;">
                            <h2 class="mb-4 fw-bold text-center" style="font-size:1.4rem;">Your Complaint</h2>
                            <div class="card mb-4" style="border-radius:1rem;overflow:hidden;box-shadow:0 2px 8px #e0e7ff;">
                                <div class="card-header bg-light fw-bold" style="background:#f8fafc;font-weight:600;">History</div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <span class="badge rounded-pill bg-secondary-soft me-2" style="background:#e0e7ff;color:#6366f1;">Created At</span>
                                            </span>
                                            <span class="text-muted small">{{ $complaint->created_at->format('M j, Y H:i') }}</span>
                                        </li>
                                        @if ($complaint->statusHistories && $complaint->statusHistories->count())
                                            @foreach ($complaint->statusHistories as $history)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <span class="badge rounded-pill me-2" style="background:#c7d2fe;color:#6366f1;">{{ $history->status }}</span>
                                                        <span class="fw-semibold">{{ $history->comment ?? '' }}</span>
                                                    </span>
                                                    <span class="text-muted small">{{ $history->created_at->format('M j, Y H:i') }}</span>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="complaint-card p-4 mb-4" style="border-radius:1rem;background:#f8fafc;box-shadow:0 2px 8px #e0e7ff;">
                                <div class="d-flex justify-content-between align-items-center mb-3" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                                    <div>
                                        <span class="complaint-card__id text-muted small">Tracking ID:# {{ substr($complaint->tracking_id, 0, 8) }}</span>
                                        <h5 class="complaint-card__name fw-bold text-dark mb-0 mt-1" style="margin:0.5rem 0 0.2rem 0;font-size:1.1rem;">{{ $complaint->name }}</h5>
                                        <div class="text-muted small">{{ $complaint->email }}</div>
                                    </div>
                                    <span class="complaint-card__status badge rounded-pill" style="background:#c7d2fe;color:#6366f1;padding:0.5em 1em;">{{ $complaint->status }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small">Complaint:</span>
                                    <div class="complaint-card__desc mt-1">{{ $complaint->complaint }}</div>
                                </div>
                                @if ($complaint->file)
                                    <div class="mb-2">
                                        <span class="text-muted small">Attachment:</span>
                                        <a href="{{ asset($complaint->file) }}" target="_blank"
                                            class="complaint-card__attachment text-primary small text-decoration-none ms-2" style="color:#6366f1;text-decoration:underline;margin-left:0.5em;">
                                            <i class="bi bi-paperclip"></i> Download
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>

</html>
