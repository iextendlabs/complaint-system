@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-4">Complaints Dashboard</h1>
            <button type="button" id="exportCsvBtn" class="btn btn-success btn-sm">Export CSV</button>
        </div>
        <!-- Filters -->
        <form method="GET" action="{{ route('home') }}" class="mb-4">
            <div class="filter-card">
                <h5 class="mb-3 fw-bold">Filter Complaints</h5>
                <div class="row g-3 align-items-center">
                    <div class="col-md-6 col-lg-2">
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Name"
                            value="{{ $filters['name'] ?? '' }}">
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Email"
                            value="{{ $filters['email'] ?? '' }}">
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center">
                            <input type="date" name="from_date" class="form-control form-control-sm me-2"
                                value="{{ $filters['from_date'] ?? '' }}">
                            <input type="date" name="to_date" class="form-control form-control-sm"
                                value="{{ $filters['to_date'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            @foreach (config('complaints.statuses') as $status => $color)
                                <option value="{{ $status }}"
                                    {{ ($filters['status'] ?? '') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confidential" value="1"
                                    id="confidentialCheck"
                                    {{ isset($filters['confidential']) && $filters['confidential'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="confidentialCheck">Confidential</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="accepted" value="1"
                                    id="acceptedCheck"
                                    {{ isset($filters['accepted']) && $filters['accepted'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="acceptedCheck">Declaration
                                    Accepted</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                        <a href="{{ route('home') }}" class="btn btn-light btn-sm">Clear</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="row g-4" id="complaintsList">
            @forelse ($complaints as $complaint)
                <div class="col-12 col-md-6 col-lg-4 complaint-item" data-status="{{ $complaint->status }}"
                    data-confidential="{{ $complaint->isConfidential ? 'true' : 'false' }}"
                    data-accepted="{{ $complaint->declarationAccepted ? 'true' : 'false' }}"
                    data-date="{{ $complaint->created_at->format('Y-m-d') }}" data-name="{{ $complaint->name }}"
                    data-email="{{ $complaint->email }}" style="animation-delay: {{ $loop->index * 50 }}ms">
                    <div class="complaint-card-new status-{{ Str::slug($complaint->status) }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 150px;"
                                        title="{{ $complaint->name }}">{{ $complaint->name }}</h6>
                                    <small class="text-muted">ID: {{ substr($complaint->tracking_id, 0, 8) }}</small>
                                </div>
                            </div>
                            <span
                                class="badge rounded-pill bg-{{ config('complaints.statuses')[$complaint->status] ?? 'secondary' }}-soft">{{ $complaint->status }}</span>
                        </div>

                        <p class="small my-3 flex-grow-1">
                            {{ Str::limit($complaint->complaint, 120) }}
                        </p>

                        @if ($complaint->isConfidential || $complaint->file)
                            <div class="mb-3">
                                @if ($complaint->isConfidential)
                                    <span class="badge bg-danger-soft text-danger small">Confidential</span>
                                @endif
                                @if ($complaint->file)
                                    <a href="{{ asset('storage/' . $complaint->file) }}" target="_blank"
                                        class="text-primary small text-decoration-none ms-2">
                                        <i class="bi bi-paperclip"></i> Attachment
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <small class="text-muted fs-xs"><i class="bi bi-clock me-1"></i>
                                {{ $complaint->created_at->format('M j, Y') }}</small>
                            <button
                                class="btn btn-sm btn-link text-primary fw-bold text-decoration-none p-0 update-status-btn"
                                data-id="{{ $complaint->id }}" data-bs-toggle="modal"
                                data-bs-target="#statusModal">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center small">
                        No complaints found.
                    </div>
                </div>
            @endforelse
        </div>

        @if ($complaints->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $complaints->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="statusForm">
                        <input type="hidden" id="complaintId">
                        <select class="form-select form-select-sm mb-2" id="statusSelect">
                            @foreach (config('complaints.statuses') as $status => $color)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('exportCsvBtn').addEventListener('click', function() {
                window.location.href = '{{ route('complaints.export') }}' + window.location.search;
            });

            let currentComplaintId = null;
            document.querySelectorAll('.update-status-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentComplaintId = this.dataset.id;
                    document.getElementById('complaintId').value = currentComplaintId;

                    const item = this.closest('.complaint-item');
                    const status = item.dataset.status;
                    document.getElementById('statusSelect').value = status;
                });
            });

            document.getElementById('statusForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const status = document.getElementById('statusSelect').value;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/complaints/${currentComplaintId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            status
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert('Updated!');
                            location.reload();
                        } else {
                            alert('Error.');
                        }
                    });
            });
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7fc;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .complaint-item {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }

        .complaint-card-new {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid transparent;
        }

        .complaint-card-new.status-open {
            background: linear-gradient(to bottom, #eef5ff, #ffffff);
            border-color: #dbeaff;
        }

        .complaint-card-new.status-in-progress {
            background: linear-gradient(to bottom, #fff8e1, #ffffff);
            border-color: #ffefc2;
        }

        .complaint-card-new.status-resolved {
            background: linear-gradient(to bottom, #f0fff4, #ffffff);
            border-color: #dcfce7;
        }

        .complaint-card-new.status-closed {
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            border-color: #e9ecef;
        }


        .complaint-card-new:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -4px rgba(0, 0, 0, 0.07);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.15) !important;
            color: #b88a00 !important;
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.15) !important;
            color: #007a92 !important;
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.15) !important;
            color: #146c43 !important;
        }
        

        .bg-secondary-soft {
            background-color: rgba(108, 117, 125, 0.15) !important;
            color: #566069 !important;
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }

        .fs-xs {
            font-size: 0.8rem;
        }

        .filter-card {
            background-color: #fff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem !important;
            border: none;
        }

        .filter-card .form-control-sm,
        .filter-card .form-select-sm {
            border-radius: 0.5rem;
            border-color: #e2e8f0;
            padding: 0.4rem 0.75rem;
        }

        .filter-card .form-control-sm:focus,
        .filter-card .form-select-sm:focus {
            box-shadow: 0 0 0 2px rgba(126, 58, 242, 0.25);
            border-color: #a57bf7;
        }

        .filter-card .btn-primary {
            background-color: #7e3af2;
            border-color: #7e3af2;
            font-weight: 600;
            padding: 0.4rem 1rem;
        }

        .filter-card .btn-primary:hover {
            background-color: #6c2bd9;
            border-color: #6c2bd9;
        }

        .filter-card .btn-light {
            border-color: #e2e8f0;
        }
    </style>
@endsection