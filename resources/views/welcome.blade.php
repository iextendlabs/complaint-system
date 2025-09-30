@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <main class="w-full lg:max-w-4xl max-w-[335px] mt-8">
            <div class="simple-card">
                <h2 class="text-xl font-semibold mb-4">Track Your Complaint</h2>
                <form method="GET" action="{{ route('tracking.get') }}" class="space-y-4">
                    <div>
                        <label for="tracking_id" class="block text-sm font-medium mb-1">Tracking ID</label>
                        <input type="text" id="tracking_id" name="tracking_id"
                            class="w-full border border-[#e3e3e0] rounded-sm px-3 py-2" required
                            value="{{ old('tracking_id') }}">
                    </div>
                    <button type="submit"
                        class="bg-[#1b1b18] text-white px-4 py-2 rounded-sm hover:bg-black transition">Track</button>
                </form>
                @if ($errors->any())
                    <div class="mt-3 text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <style>
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
@endsection
