@extends('layouts.sidebar')
@section('title', 'Invigilators Assignment')

@section('body')
<div class="content" style="padding-bottom: 120px; background-color: #f8f9fa;">
    
    {{-- 1. Modern Header & KPI Pills --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">إدارة المراقبات</h3>
            <div class="text-muted small d-flex align-items-center">
                <i class="fa-solid fa-layer-group me-2"></i>
                <span>توزيع المراقبين على القاعات (عرض شامل)</span>
            </div>
        </div>

        <div class="d-flex gap-3">
            <div class="stat-pill bg-white shadow-sm border">
                <div class="icon bg-primary-subtle text-primary"><i class="fa-regular fa-calendar-check"></i></div>
                <div class="info">
                    <span class="value">{{ count($allocations) }}</span>
                    <span class="label">Days</span>
                </div>
            </div>
            <div class="stat-pill bg-white shadow-sm border">
                <div class="icon bg-success-subtle text-success"><i class="fa-solid fa-user-tie"></i></div>
                <div class="info">
                    <span class="value">{{ $invigilators->count() }}</span>
                    <span class="label">Staff</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Search Toolbar --}}
    <div class="search-toolbar-wrapper mb-4">
        <div class="search-toolbar bg-white shadow-sm rounded-pill border d-flex align-items-center p-1 ps-4">
            <div class="d-flex align-items-center flex-grow-1">
                <i class="fa-solid fa-magnifying-glass text-muted me-3"></i>
                <div class="d-flex align-items-center">
                    <span class="text-muted small fw-bold me-2 text-nowrap">Filter Date:</span>
                    <input type="date" id="dateSearchInput" autocomplete="off" class="form-control border-0 bg-transparent shadow-none p-0" style="max-width: 150px; color: #495057; font-weight: 600;">
                </div>
            </div>
            <button type="button" id="resetSearchBtn" class="btn btn-light rounded-pill px-3 text-muted border-0 hover-bg-gray">
                <i class="fa-solid fa-rotate-right"></i>
                <span class="d-none d-md-inline ms-1">Reset</span>
            </button>
        </div>
    </div>

    {{-- 3. Main Form --}}
    <form action="{{ route('invigilation.save_global') }}" method="POST" id="invigilationForm">
        @csrf

        @if($allocations->isEmpty())
            <div class="empty-state text-center py-5 text-muted">
                <i class="fa-solid fa-calendar-xmark fa-4x mb-3 opacity-25"></i>
                <h4 class="fw-bold">No Data Available</h4>
                <p>Please generate the exam schedule first.</p>
            </div>
        @else
            <div id="allocationsContainer">
                @foreach($allocations as $date => $dayAllocations)
                    @php
                        $isUnscheduled = ($date == '0000-00-00' || $date == null || $date == '');
                        
                        if (!$isUnscheduled) {
                            try {
                                $carbonDate = \Carbon\Carbon::parse($date);
                                // 1. Search Value (Y-m-d)
                                $dateValue = $carbonDate->format('Y-m-d');
                                // 2. Header Title (Monday, 09 December 2025)
                                $formattedDate = $carbonDate->format('l, d F Y');
                                // 3. Badge Content (Short Month & Day)
                                $badgeMonth = $carbonDate->format('M'); 
                                $badgeDay = $carbonDate->format('d');   
                            } catch (\Exception $e) {
                                $isUnscheduled = true;
                            }
                        }

                        // Fallback
                        if ($isUnscheduled) {
                            $dateValue = '0000-00-00';
                            $formattedDate = 'Unscheduled';
                            $badgeMonth = '--';
                            $badgeDay = '!';
                        }
                    @endphp

                    {{-- Card Container --}}
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden rounded-4 date-card" data-date="{{ $dateValue }}">
                        
                        {{-- Card Header --}}
                        <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center bg-white">
                            <div class="d-flex align-items-center gap-3">
                                
                                {{-- Date Badge --}}
                                <div class="date-badge shadow-sm {{ $isUnscheduled ? 'bg-danger' : 'bg-primary' }}">
                                    <span class="month">{{ $badgeMonth }}</span>
                                    <span class="day">{{ $badgeDay }}</span>
                                </div>
                                
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark">{{ $formattedDate }}</h5>
                                    <span class="small text-muted">{{ count($dayAllocations) }} Halls</span>
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 custom-table">
                                    <thead>
                                        <tr>
                                            <th class="ps-4" width="10%">Slot</th>
                                            <th width="20%">Room Details</th>
                                            <th width="25%">Course</th>
                                            <th width="45%" class="pe-4">Assign Invigilators</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dayAllocations as $alloc)
                                            <tr>
                                                <td class="ps-4">
                                                    <span class="slot-badge">{{ $alloc->slot_name }}</span>
                                                </td>
                                                <td>
                                                    <div class="room-info">
                                                        <span class="room-name">{{ $alloc->room_name }}</span>
                                                        <span class="room-cap"><i class="fa-solid fa-users"></i> {{ $alloc->student_count }} Students</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="course-title text-truncate" style="max-width: 200px;" title="{{ $alloc->course_name }}">
                                                            {{ $alloc->course_name ?? '---' }}
                                                        </span>
                                                        <div class="d-flex gap-1 mt-1">
                                                            <span class="meta-badge code">{{ $alloc->course_code ?? 'CODE' }}</span>
                                                            <span class="meta-badge dept">{{ $alloc->department_info }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="pe-4 py-3">
                                                    @if($isUnscheduled)
                                                        <div class="alert alert-warning mb-0 py-1 small px-2 d-inline-flex align-items-center">
                                                            <i class="fa-solid fa-clock me-1"></i> Wait for Schedule
                                                        </div>
                                                    @else
                                                        @php
                                                            $assigned = $currentAssignments[$alloc->allocation_id][$alloc->exam_date_id] ?? [];
                                                        @endphp
                                                        
                                                        {{-- [CRITICAL FIX] Hidden Input to detect unassignment --}}
                                                        <input type="hidden" name="present[{{ $alloc->allocation_id }}][{{ $alloc->exam_date_id }}]" value="1">

                                                        <select name="assignments[{{ $alloc->allocation_id }}][{{ $alloc->exam_date_id }}][]" 
                                                                class="form-select select2-staff" 
                                                                multiple="multiple">
                                                            @foreach($invigilators as $inv)
                                                                <option value="{{ $inv->id }}" {{ in_array($inv->id, $assigned) ? 'selected' : '' }}>
                                                                    {{ $inv->user->name ?? 'Unknown' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 4. Sticky Footer --}}
        <div class="fixed-bottom bg-white border-top shadow-lg p-3" style="margin-right: 250px; z-index: 1020;">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="fa-solid fa-circle-check text-success fa-lg"></i>
                    <small class="fw-bold">Ready to save.</small>
                </div>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow-sm hover-scale">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Assignments
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Styles --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* 1. Stat Pills */
    .stat-pill { display: flex; align-items: center; padding: 5px 15px 5px 5px; border-radius: 50px; gap: 12px; min-width: 140px; }
    .stat-pill .icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .stat-pill .info { display: flex; flex-direction: column; line-height: 1.1; }
    .stat-pill .value { font-weight: 800; font-size: 1.1rem; color: #2d3748; }
    .stat-pill .label { font-size: 0.7rem; color: #718096; text-transform: uppercase; }

    /* 2. DATE BADGE (Fixed) */
    .date-badge {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: center; 
        align-items: center;
        text-align: center;
        line-height: 1;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        color: white !important;
    }
    .date-badge.bg-primary { background-color: #0d6efd !important; }
    .date-badge.bg-danger { background-color: #dc3545 !important; }

    .date-badge .month {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        opacity: 0.9;
        margin-bottom: 3px;
        display: block;
    }
    .date-badge .day {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1;
        display: block;
    }

    /* 3. Tables */
    .custom-table thead th { background: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; }
    .slot-badge { background: #edf2f7; color: #4a5568; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }
    .room-name { display: block; font-weight: 700; color: #2d3748; font-size: 0.95rem; }
    .room-cap { font-size: 0.75rem; color: #a0aec0; }
    .course-title { font-weight: 600; color: #434190; font-size: 0.9rem; }
    .meta-badge { font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; }
    .meta-badge.code { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; font-family: monospace; }
    .meta-badge.dept { background: #fff; color: #718096; }

    /* 4. Select2 */
    .select2-container--default .select2-selection--multiple { border: 1px solid #e2e8f0 !important; border-radius: 8px !important; min-height: 42px; padding: 4px; }
    .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #f7fafc; border: 1px solid #e2e8f0; color: #2d3748; border-radius: 6px; margin-top: 2px; padding-left: 20px; position: relative; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { position: absolute; left: 0; top: 0; bottom: 0; width: 20px; border: none; border-right: 1px solid #e2e8f0; background: transparent; color: #e53e3e; display: flex; align-items: center; justify-content: center; }

    .hover-bg-gray:hover { background-color: #e2e8f0; }
    .hover-scale:hover { transform: translateY(-2px); transition: 0.2s; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Init Select2
        $('.select2-staff').select2({
            width: '100%', dir: 'rtl', placeholder: "Select Invigilators...",
            language: { noResults: () => "No invigilators found" }
        });

        // Search Logic
        $('#dateSearchInput').on('change input', function() {
            let selectedDate = $(this).val(); 

            if (selectedDate && selectedDate.trim() !== "") {
                $('.date-card').each(function() {
                    let cardDate = $(this).attr('data-date'); 
                    if (cardDate === selectedDate) {
                        $(this).fadeIn();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $('.date-card').fadeIn();
            }
        });

        $('#resetSearchBtn').click(function() {
            $('#dateSearchInput').val('').trigger('change');
        });
    });
</script>
@endsection