@extends('layouts.app')

@section('content')
    <style>
        .otp-table-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 32px 24px 24px 24px;
            margin-bottom: 32px;
        }
        .otp-table-container h1 {
            font-weight: 700;
            color: #2c3e50;
        }
        .otp-table-container table {
            margin-top: 16px;
            border-radius: 8px;
            overflow: hidden;
        }
        .otp-table-container th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
        }
        .otp-table-container td {
            vertical-align: middle;
        }
        .otp-table-container .text-center {
            color: #9ca3af;
        }
        /* Stylish filter sidebar styles */
        .sidebar-filter {
            background: #f9fafb;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: 1px solid #e5e7eb;
        }
        .filter__label {
            font-size: 0.95rem;
            color: #6b7280;
        }
        .filter__input.form-control,
        .filter__input.form-select {
            border-radius: 50px;
            border: 1px solid #d1d5db;
            font-size: 0.97rem;
            padding-left: 16px;
        }
        .filter__input--date {
            min-width: 120px;
        }
        .filter__date-range .text-muted {
            font-size: 1.1em;
        }
        .filter__actions .btn-primary {
            background: #2563eb;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .filter__actions .btn-primary:hover {
            background: #1d4ed8;
        }
        .filter__actions .btn-light {
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .filter__actions .btn-light:hover {
            background: #e5e7eb;
            color: #111827;
        }
    </style>
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-9 order-2 order-lg-1">
                <div class="otp-table-container">
                    <h1 class="mb-4">OTP List</h1>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Number</th>
                                <th>OTP</th>
                                <th>Tracking Id</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($otps as $otp)
                                <tr>
                                    <td>{{ $otp->name }}</td>
                                    <td>{{ $otp->email }}</td>
                                    <td>{{ $otp->number }}</td>
                                    <td>{{ $otp->otp }}</td>
                                    <td>{{ $otp->tracking_id }}</td>
                                    <td>{{ $otp->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No OTPs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div>
                        {{ $otps->withQueryString()->links() }}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 order-1 order-lg-2 mb-4 mb-lg-0">
                <form method="GET" action="{{ url('/otps') }}">
                    <div class="filter filter--stylish sidebar-filter p-4 mb-4 mb-lg-0">
                        <div class="filter__fields d-flex flex-column gap-4">
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted" for="filterName">Name</label>
                                <input id="filterName" type="text" name="name" class="filter__input form-control form-control-sm rounded-pill" placeholder="Name" value="{{ $filters['name'] }}">
                            </div>
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted" for="filterEmail">Email</label>
                                <input id="filterEmail" type="email" name="email" class="filter__input form-control form-control-sm rounded-pill" placeholder="Email" value="{{ $filters['email'] }}">
                            </div>
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted" for="filterNumber">Phone</label>
                                <input id="filterNumber" type="text" name="number" class="filter__input form-control form-control-sm rounded-pill" placeholder="Phone" value="{{ $filters['number'] }}">
                            </div>
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted">Date</label>
                                <div class="filter__date-range d-flex flex-wrap gap-2 align-items-center">
                                    <input type="date" name="from_date" class="filter__input filter__input--date form-control form-control-sm rounded-pill" value="{{ $filters['from_date'] }}">
                                    <span class="mx-1 text-muted">-</span>
                                    <input type="date" name="to_date" class="filter__input filter__input--date form-control form-control-sm rounded-pill" value="{{ $filters['to_date'] }}">
                                </div>
                            </div>
                            <div class="filter__actions d-flex gap-2 align-items-end mt-2">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Apply</button>
                                <a href="{{ url('/otps') }}" class="btn btn-light btn-sm rounded-pill px-3">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
