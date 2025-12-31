@extends('layouts.sidebar')
@section('title', 'الجدولة اليدوية')

@section('body')
<div class="content">
    {{-- FLOATING ALERTS SYSTEM --}}
<div class="toast-container">
    @if(session('success'))
        <div class="toast-message success">
            <div class="icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="text">
                <strong>نجاح</strong>
                <p>{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if(session('error'))
        <div class="toast-message error">
            <div class="icon"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="text">
                <strong>خطأ</strong>
                <p>{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if(session('warning'))
        <div class="toast-message warning">
            <div class="icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="text">
                <strong>تنبيه</strong>
                <p>{{ session('warning') }}</p>
            </div>
            <button onclick="this.parentElement.remove()">×</button>
        </div>
    @endif
</div>
    <div class="topbar" style="margin-bottom: 25px;">
        <h2 class="section-title">الجدولة اليدوية: {{ $slot->slot_name }}</h2>
        <div class="text-muted" style="font-size: 13px;">
            <i class="fa-solid fa-swatchbook"></i> كل مادة لها "لون هوية" خاص. المربعات الملونة تشير إلى المواد التي تتعارض معها.
        </div>
    </div>

    <form action="{{ route('schedule.manual.save', $slot->id) }}" method="POST">
        @csrf

        {{-- Loop through Groups (Department Levels) --}}
        @foreach($groupedCourses as $deptLevelId => $group)
            <div class="card mb-4" style="border: 1px solid var(--border); overflow: hidden;">
                <div class="card-header" style="background: #f1f5f9; padding: 15px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                    <h4 style="margin: 0; color: var(--sidebar); font-size: 16px;">
                        <i class="fa-solid fa-layer-group me-2"></i> {{ $group['title'] }}
                    </h4>
                    <span class="badge bg-secondary">{{ count($group['courses']) }} مواد</span>
                </div>

                <div class="card-body" style="padding: 0;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">هوية</th>
                                <th style="width: 30%;">المادة</th>
                                <th style="width: 30%;">
                                    التعارضات (Multi-Color) 
                                    <i class="fa-solid fa-circle-question text-muted" title="الألوان تشير إلى هوية المواد المتعارضة"></i>
                                </th>
                                <th style="width: 35%;">تحديد التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['courses'] as $course)
                                <tr>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <div style="width: 20px; height: 20px; background: {{ $course['identity_color'] }}; border-radius: 4px; border: 1px solid #ccc; margin: 0 auto;" 
                                             title="لون هوية المادة" data-bs-toggle="tooltip"></div>
                                    </td>

                                    <td style="vertical-align: middle;">
                                        <strong>{{ $course['name'] }}</strong>
                                        <div class="text-muted" style="font-size: 11px;">{{ $course['code'] ?? '' }}</div>
                                    </td>

                                    <td style="vertical-align: middle;">
                                        @if(count($course['conflict_colors']) > 0)
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                @foreach($course['conflict_colors'] as $conflict)
                                                    <div class="conflict-dot" 
                                                         style="background: {{ $conflict['color'] }};" 
                                                         title="تعارض مع: {{ $conflict['name'] }} {{ isset($conflict['code']) ? '('.$conflict['code'].')' : '' }}"
                                                         data-bs-toggle="tooltip">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small style="font-size: 10px; color: var(--danger);">
                                                {{ count($course['conflict_colors']) }} تقاطعات
                                            </small>
                                        @else
                                            <span class="badge bg-success" style="font-size: 10px; font-weight: normal;">آمن (لا يوجد تعارض)</span>
                                        @endif
                                    </td>

                                    <td style="vertical-align: middle;">
                                        <div style="position: relative;">
                                            <select name="schedule[{{ $deptLevelId }}][{{ $course['composite_key'] }}]" 
                                                    class="form-control schedule-select"
                                                    data-id="{{ $course['id'] }}"
                                                    data-name="{{ $course['name'] }}"
                                                    data-conflicts='@json($course['conflicts'] ?? [])'
                                                    onchange="validateGraph()"
                                                    style="font-size: 13px;">
                                                
                                                <option value="">-- اختر يوماً --</option>
                                                @foreach($dates as $date)
                                                    {{-- THIS LOGIC FILLS THE DATES --}}
                                                    @php
                                                        $isSelected = isset($savedSchedule[$course['id']]) && $savedSchedule[$course['id']] == $date->id;
                                                    @endphp
                                                    <option value="{{ $date->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::parse($date->actual_date)->format('Y-m-d') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="error-{{ $course['id'] }}" class="conflict-msg" style="display: none; color: red; font-size: 11px; margin-top: 2px;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> تعارض!
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="footer-actions" style="position: sticky; bottom: 20px; background: white; padding: 15px; border-top: 2px solid var(--sidebar); z-index: 100; box-shadow: 0 -5px 20px rgba(0,0,0,0.1); border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span id="conflict-count" class="badge bg-success">0 تعارضات حالية</span>
                </div>
                <button type="submit" id="saveBtn" class="btn add" style="padding: 10px 40px;">حفظ الجدول النهائي</button>
            </div>
        </div>
    </form>
</div>

<style>
    .conflict-dot {
        width: 15px; height: 15px; border-radius: 3px; cursor: help; 
        border: 1px solid rgba(0,0,0,0.1); transition: transform 0.2s;
    }
    .conflict-dot:hover { transform: scale(1.3); z-index: 10; }



    /* Container for floating messages */
    .toast-container {
        position: fixed;
        top: 30px;
        left: 50%;
        transform: translateX(-50%); /* Center it horizontally */
        z-index: 10000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 90%;
        max-width: 400px;
    }

    /* The Message Box */
    .toast-message {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 15px;
        border-right: 5px solid #ddd; /* Right border for color indication */
        animation: slideDown 0.4s ease-out forwards;
        direction: rtl; /* Right-to-left for Arabic */
    }

    /* Colors */
    .toast-message.success { border-right-color: #10b981; }
    .toast-message.success .icon { color: #10b981; }
    
    .toast-message.error { border-right-color: #ef4444; }
    .toast-message.error .icon { color: #ef4444; }

    .toast-message.warning { border-right-color: #f59e0b; }
    .toast-message.warning .icon { color: #f59e0b; }

    /* Text Styling */
    .toast-message .text { flex: 1; }
    .toast-message .text strong { display: block; font-size: 14px; margin-bottom: 2px; }
    .toast-message .text p { margin: 0; font-size: 13px; color: #666; }
    .toast-message .icon { font-size: 20px; }

    /* Close Button */
    .toast-message button {
        background: none; border: none; font-size: 20px; color: #999; cursor: pointer;
    }
    .toast-message button:hover { color: #333; }

    /* Animation */
    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes fadeOut {
        to { opacity: 0; transform: translateY(-20px); }
    }



</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });
        
        // Validate immediately to show conflicts if Auto-Generate made any mistakes (rare but possible)
        validateGraph();
    });

    function validateGraph() {
        const selects = document.querySelectorAll('.schedule-select');
        let totalConflicts = 0;

        selects.forEach(sel => {
            sel.style.borderColor = '#cbd5e1';
            sel.style.backgroundColor = '#fff';
            document.getElementById(`error-${sel.dataset.id}`).style.display = 'none';
        });

        // Use String keys for ID matching
        const currentSelections = new Map();
        selects.forEach(s => {
            if(s.value) currentSelections.set(String(s.dataset.id), s.value);
        });

        selects.forEach(currentSelect => {
            const myId = String(currentSelect.dataset.id);
            const myDate = currentSelect.value;
            
            if (!myDate) return;

            let myConflicts = [];
            try {
                myConflicts = typeof currentSelect.dataset.conflicts === 'string' 
                              ? JSON.parse(currentSelect.dataset.conflicts) 
                              : currentSelect.dataset.conflicts;
            } catch (e) { console.error(e); }

            let conflictFound = false;

            myConflicts.forEach(neighborIdRaw => {
                const neighborId = String(neighborIdRaw);
                if (currentSelections.has(neighborId) && currentSelections.get(neighborId) === myDate) {
                    conflictFound = true;
                    const neighborSelect = document.querySelector(`.schedule-select[data-id="${neighborId}"]`);
                    if (neighborSelect) {
                        neighborSelect.style.borderColor = 'var(--danger)';
                        neighborSelect.style.backgroundColor = '#fff5f5';
                    }
                }
            });

            if (conflictFound) {
                totalConflicts++;
                currentSelect.style.borderColor = 'var(--danger)';
                currentSelect.style.backgroundColor = '#fff5f5';
                document.getElementById(`error-${myId}`).style.display = 'block';
            }
        });

        const badge = document.getElementById('conflict-count');
        const btn = document.getElementById('saveBtn');

        if (totalConflicts > 0) {
            badge.className = 'badge bg-danger';
            badge.innerText = totalConflicts + ' تعارضات حالية';
            btn.disabled = true;
            btn.style.opacity = '0.6';
        } else {
            badge.className = 'badge bg-success';
            badge.innerText = 'جدول سليم (0 تعارض)';
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    }
    // Automatically hide after 4 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const toasts = document.querySelectorAll('.toast-message');
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.5s forwards';
                setTimeout(() => toast.remove(), 500); // Remove from DOM after fade
            }, 4000);
        });
    });
</script>
@endsection