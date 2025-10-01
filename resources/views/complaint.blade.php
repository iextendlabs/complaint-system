@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4 fw-bold text-center">Your Complaint</h2>
                @if (isset($error))
                    <div class="alert alert-danger mt-3 text-center">{{ $error }}</div>
                @endif

                @if (isset($complaint))
                    <div class="card mb-4">
                        <div class="card-header bg-light fw-bold">History</div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="badge rounded-pill bg-secondary-soft me-2">Created At</span>

                                    </span>
                                    <span class="text-muted small">{{ $complaint->created_at->format('M j, Y H:i') }}</span>
                                </li>
                                @if ($complaint->statusHistories && $complaint->statusHistories->count())
                                    @foreach ($complaint->statusHistories as $history)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <span
                                                    class="badge rounded-pill bg-{{ config('complaints.statuses')[$history->status] ?? 'secondary' }}-soft me-2">{{ $history->status }}</span>
                                                <span class="fw-semibold">{{ $history->comment ?? '' }}</span>
                                                <span class="text-muted small ms-2">
                                                    @if($history->user)
                                                        by {{ $history->user->name }}
                                                    @endif
                                                </span>
                                            </span>
                                            <span
                                                class="text-muted small">{{ $history->created_at->format('M j, Y H:i') }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="complaint-card status-{{ Str::slug($complaint->status) }} p-3 mb-3" style="min-height:unset;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="complaint-card__id text-muted small">Tracking ID:#
                                    {{ substr($complaint->tracking_id, 0, 8) }}</span>
                                <h5 class="complaint-card__name fw-bold text-dark mb-0 mt-1">{{ $complaint->name }}</h5>
                                <div class="text-muted small">{{ $complaint->email }}</div>
                                @if ($complaint->isConfidential)
                                    <span class="complaint-card__confidential badge bg-danger-soft text-danger small ms-1">Confidential</span>
                                @endif
                            </div>
                            <span class="complaint-card__status badge rounded-pill bg-{{ config('complaints.statuses')[$complaint->status] ?? 'secondary' }}-soft">{{ $complaint->status }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small">Complaint:</span>
                            <div class="complaint-card__desc mt-1" style="white-space:pre-line;">{{ $complaint->complaint }}</div>
                        </div>
                        @if ($complaint->file)
                            <div class="mb-1">
                                <span class="text-muted small">Attachment:</span>
                                <a href="{{ asset($complaint->file) }}" target="_blank"
                                    class="complaint-card__attachment text-primary small text-decoration-none ms-2">
                                    <i class="bi bi-paperclip"></i> Download
                                </a>
                            </div>
                        @endif
                        
                    </div>

                @endif
            </div>
        </div>
    </div>
@endsection
