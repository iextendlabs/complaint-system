@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-4">Complaints Dashboard</h1>
            <button type="button" id="exportCsvBtn" class="btn btn-success btn-sm">Export CSV</button>
        </div>

        {{-- Reports Section --}}
        <div class="row mb-4 dashboard-reports-row">
            <div class="col-12 col-md-3 mb-2">
                <div class="dashboard-report-card text-center py-3 px-2">
                    <div class="fw-bold fs-4">{{ $reports['total'] ?? 0 }}</div>
                    <div class="text-muted">Total Complaints</div>
                </div>
            </div>
            @foreach ($reports['by_status'] ?? [] as $status => $count)
                <div class="col-12 col-md-2 mb-2">
                    <div class="dashboard-report-card text-center py-3 px-2">
                        <div class="fw-bold fs-5">{{ $count }}</div>
                        <div class="text-muted">{{ $status }}</div>
                    </div>
                </div>
            @endforeach
            <div class="col-12 col-md-3 mb-2">
                <div class="dashboard-report-card text-center py-3 px-2">
                    <div class="fw-bold fs-5">{{ $reports['recent'] ?? 0 }}</div>
                    <div class="text-muted">Last 7 Days</div>
                </div>
            </div>
        </div>
        @can('complaint-list')
        <div class="row">
            <div class="col-lg-9 order-2 order-lg-1">
                <div class="complaints-list row g-4" id="complaintsList">
                    @forelse ($complaints as $complaint)
                        <div class="complaints-list__item complaint-item col-12 col-md-6 col-lg-4"
                            style="animation-delay: {{ $loop->index * 50 }}ms">
                            <div class="complaint-card status-{{ Str::slug($complaint->status) }}">
                                <div class="complaint-card__header d-flex justify-content-between align-items-start">
                                    <div class="complaint-card__user d-flex align-items-center">
                                        <div>
                                            <small class="complaint-card__id text-muted">Tracking ID:#
                                                {{ substr($complaint->tracking_id, 0, 8) }}</small>
                                            <h6 class="complaint-card__name fw-bold text-dark mb-0 text-truncate"
                                                style="max-width: 150px;" title="{{ $complaint->name }}">
                                                {{ $complaint->name }}</h6>
                                            <p> {{ $complaint->email }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="complaint-card__status badge rounded-pill bg-{{ config('complaints.statuses')[$complaint->status] ?? 'secondary' }}-soft">{{ $complaint->status }}</span>
                                </div>

                                <p class="complaint-card__desc small my-3 flex-grow-1">
                                    {{ Str::limit($complaint->complaint, 120) }}
                                </p>

                                @if ($complaint->isConfidential || $complaint->file)
                                    <div class="complaint-card__meta mb-3">
                                        @if ($complaint->isConfidential)
                                            <span
                                                class="complaint-card__confidential badge bg-danger-soft text-danger small">Confidential</span>
                                        @endif
                                        @if ($complaint->file)
                                            <a href="{{ asset($complaint->file) }}" target="_blank"
                                                class="complaint-card__attachment text-primary small text-decoration-none ms-2">
                                                <i class="bi bi-paperclip"></i> Attachment
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <div
                                    class="complaint-card__footer d-flex justify-content-between align-items-center border-top pt-2">
                                    <small class="complaint-card__date text-muted fs-xs"><i class="bi bi-clock me-1"></i>
                                        {{ $complaint->created_at->format('M j, Y') }}</small>
                                    <div class="d-flex gap-2 align-items-center">
                                        <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-link text-info p-0" title="View">
                                            <i class="bi bi-eye fs-5"></i>
                                        </a>
                                        @can('complaint-edit')
                                        <button
                                            class="complaint-card__update-btn btn btn-sm btn-link text-primary p-0 update-status-btn"
                                            data-id="{{ $complaint->id }}" data-bs-toggle="modal"
                                            data-bs-target="#statusModal" title="Update">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        @endcan
                                        @can('complaint-delete')
                                        <button
                                            class="complaint-card__delete-btn btn btn-sm btn-link text-danger p-0 delete-complaint-btn"
                                            data-id="{{ $complaint->id }}" title="Delete">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                        @endcan
                                    </div>
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
            </div>
            <div class="col-lg-3 order-1 order-lg-2 mb-4 mb-lg-0">
                <form method="GET" action="{{ route('home') }}">
                    <div class="filter filter--stylish sidebar-filter p-4 mb-4 mb-lg-0">
                        <div class="filter__fields d-flex flex-column gap-4">
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted"
                                    for="filterName">Name</label>
                                <input id="filterName" type="text" name="name"
                                    class="filter__input form-control form-control-sm rounded-pill" placeholder="Name"
                                    value="{{ $filters['name'] ?? '' }}">
                            </div>
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted"
                                    for="filterTrackingId">Tracking ID</label>
                                <input id="filterTrackingId" type="text" name="tracking_id"
                                    class="filter__input form-control form-control-sm rounded-pill"
                                    placeholder="Tracking ID" value="{{ $filters['tracking_id'] ?? '' }}">
                            </div>
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted"
                                    for="filterEmail">Email</label>
                                <input id="filterEmail" type="text" name="email"
                                    class="filter__input form-control form-control-sm rounded-pill" placeholder="Email"
                                    value="{{ $filters['email'] ?? '' }}">
                            </div>
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted">Date</label>
                                <div class="filter__date-range d-flex flex-wrap gap-2 align-items-center">
                                    <input type="date" name="from_date"
                                        class="filter__input filter__input--date form-control form-control-sm rounded-pill"
                                        value="{{ $filters['from_date'] ?? '' }}">
                                    <span class="mx-1 text-muted">-</span>
                                    <input type="date" name="to_date"
                                        class="filter__input filter__input--date form-control form-control-sm rounded-pill"
                                        value="{{ $filters['to_date'] ?? '' }}">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="filter__label form-label mb-1 fw-semibold text-muted"
                                    for="filterStatus">Status</label>
                                <select id="filterStatus" name="status"
                                    class="filter__input form-select form-select-sm rounded-pill">
                                    <option value="">All Statuses</option>
                                    @foreach (config('complaints.statuses') as $status => $color)
                                        <option value="{{ $status }}"
                                            {{ ($filters['status'] ?? '') == $status ? 'selected' : '' }}>
                                            {{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter__switches d-flex flex-column gap-2 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="confidential" value="1"
                                        id="confidentialCheck"
                                        {{ isset($filters['confidential']) && $filters['confidential'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="confidentialCheck">Confidential</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="accepted" value="1"
                                        id="acceptedCheck"
                                        {{ isset($filters['accepted']) && $filters['accepted'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="acceptedCheck">Declaration Accepted</label>
                                </div>
                            </div>
                            <div class="filter__actions d-flex gap-2 align-items-end mt-2">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Apply</button>
                                <a href="{{ route('home') }}" class="btn btn-light btn-sm rounded-pill px-3">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($complaints->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $complaints->appends(request()->query())->links() }}
            </div>
        @endif
        @endcan
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
        $(document).ready(function() {
            $('#exportCsvBtn').on('click', function() {
                window.location.href = '{{ route('complaints.export') }}' + window.location.search;
            });

            let currentComplaintId = null;

            $('.update-status-btn').on('click', function() {
                currentComplaintId = $(this).data('id');
                $('#complaintId').val(currentComplaintId);

                let item = $(this).closest('.complaint-item');
                let status = item.length ? item.data('status') : null;
                if (!status) {
                    let card = $(this).closest('.complaint-card');
                    if (card.length) {
                        let classes = card.attr('class').split(' ');
                        let statusClass = classes.find(c => c.startsWith('status-'));
                        if (statusClass) {
                            status = statusClass.replace('status-', '').replace(/-/g, ' ');
                            $('#statusSelect option').each(function() {
                                if ($(this).val().toLowerCase() === status.toLowerCase()) {
                                    status = $(this).val();
                                }
                            });
                        }
                    }
                }
                $('#statusSelect').val(status);
            });

            $('#statusForm').on('submit', function(e) {
                e.preventDefault();

                const status = $('#statusSelect').val();
                const token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: `/complaints/${currentComplaintId}/update-status`,
                    method: 'POST',
                    data: JSON.stringify({
                        status
                    }),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function(data) {
                        if (data.success) {
                            alert('Updated!');
                            location.reload();
                        } else {
                            alert('Error.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while updating.');
                    }
                });
            });
            $('.delete-complaint-btn').on('click', function() {
                const id = $(this).data('id');
                if (!confirm('Are you sure you want to delete this complaint?')) return;
                const token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: `/complaints/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function(data) {
                        if (data.success) {
                            alert('Deleted!');
                            location.reload();
                        } else {
                            alert('Error deleting complaint.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while deleting.');
                    }
                });
            });
        });
    </script>

@endsection
