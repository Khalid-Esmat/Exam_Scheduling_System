@extends('layouts.sidebar')
@section('title', 'إدارة الجدولة')

@section('body')
<div class="content" style="padding-bottom: 80px;">
    <div class="topbar" style="margin-bottom: 30px;">
        <h2 class="section-title">لوحة التحكم بالجدولة الدراسية</h2>
        <p class="text-muted">اختر طريقة الجدولة المناسبة لكل مجموعة امتحانات.</p>
    </div>

    @if($slots->isEmpty())
        <div class="alert alert-info text-center">
            لا توجد مجموعات امتحانات نشطة حالياً. قم بإنشاء مجموعة أولاً.
        </div>
    @endif

    <div class="row">
        @foreach($slots as $slot)
            <div class="col-12 mb-4">
                <div class="card schedule-card-row" style="overflow: hidden; border: 1px solid var(--border); border-right: 5px solid var(--sidebar);">
                    <div class="card-body" style="padding: 0;">
                        <div class="row g-0">
                            
                            {{-- Info Section --}}
                            <div class="col-md-4 info-section" style="padding: 20px; background: #fff;">
                                <h4 style="color: var(--primary); margin-bottom: 10px; font-weight: 700;">
                                    {{ $slot->slot_name }}
                                </h4>
                                
                                <div style="font-size: 13px; color: var(--muted); margin-bottom: 15px;">
                                    <i class="fa-solid fa-calendar-days me-1"></i> {{ $slot->examDates->count() }} أيام امتحانات
                                    <br>
                                    <i class="fa-solid fa-book-open me-1"></i> {{ $slot->total_courses_count }} مادة دراسية
                                </div>

                                @if($slot->is_scheduled)
                                    <span class="badge bg-success"><i class="fa-solid fa-check"></i> تم التوليد</span>
                                @else
                                    <span class="badge bg-secondary">بانتظار الجدولة</span>
                                @endif
                            </div>

                            {{-- Actions Section --}}
                            <div class="col-md-8 actions-section" style="display: flex;">
                                
                                <a href="{{ route('schedule.manual', $slot->id) }}" class="action-box manual-box">
                                    <div class="icon-wrapper">
                                        <i class="fa-solid fa-hand-pointer"></i>
                                    </div>
                                    <div class="text-wrapper">
                                        <h5>الجدولة اليدوية</h5>
                                        <small>التحكم الكامل في توزيع المواد (Drag & Drop)</small>
                                    </div>
                                    <i class="fa-solid fa-arrow-left arrow-icon"></i>
                                </a>
                               
                                <div class="action-box auto-box" onclick="confirmAuto('{{ $slot->id }}', '{{ $slot->slot_name }}')">
                                    <div class="icon-wrapper">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                                    </div>
                                    <div class="text-wrapper">
                                        <h5>الجدولة التلقائية</h5>
                                        <small>استخدام خوارزمية Graph Coloring لمنع التعارضات</small>
                                    </div>
                                    <i class="fa-solid fa-gear arrow-icon"></i>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ========================================== --}}
    {{-- NEW: INVIGILATION CTA BUTTON               --}}
    {{-- ========================================== --}}
<div class="mt-5">
    {{-- Card Container --}}
    <div class="card border-0 shadow bg-white rounded-4 overflow-hidden position-relative">
        
        {{-- Decorative Accent Line (Right Side) --}}
        <div class="position-absolute top-0 end-0 h-100 bg-warning" style="width: 6px;"></div>

        <div class="card-body p-4 p-md-5 d-flex flex-wrap justify-content-between align-items-center gap-4">
            
            {{-- Right Side: Icon & Text --}}
            <div class="d-flex align-items-center gap-4">
                {{-- Icon Box --}}
                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" 
                     style="width: 70px; height: 70px; background-color: #fff8e1; color: #f59e0b; font-size: 28px;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                
                {{-- Text --}}
                <div>
                    <h4 class="fw-bold text-dark mb-1">توزيع المراقبين واللجان</h4>
                    <p class="text-muted mb-0">بعد الانتهاء من جدولة المواد، انتقل لتوزيع المراقبين على القاعات.</p>
                </div>
            </div>
            
            {{-- Left Side: Button --}}
            <div>
                <a href="{{ route('invigilation.global') }}" class="btn btn-warning px-5 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2 transition-hover">
                    <span>الذهاب للمراقبات</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
            
        </div>
    </div>
</div>


    

<style>
    .schedule-card-row {
        transition: transform 0.2s;
        border-radius: 12px;
    }
    .schedule-card-row:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .action-box {
        flex: 1;
        display: flex;
        align-items: center;
        padding: 20px;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s;
        border-left: 1px solid #eee;
        position: relative;
    }

    /* Manual Style */
    .manual-box { background: #f8fafc; color: var(--sidebar); }
    .manual-box:hover { background: #e2e8f0; }
    .manual-box .icon-wrapper { color: var(--sidebar); background: #fff; border: 1px solid var(--sidebar); }

    /* Auto Style */
    .auto-box { background: #fdf4ff; color: #9333ea; } /* Purple theme for Magic */
    .auto-box:hover { background: #fce7f3; }
    .auto-box .icon-wrapper { color: #9333ea; background: #fff; border: 1px solid #9333ea; }

    .icon-wrapper {
        width: 50px; height: 50px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        margin-left: 15px;
    }
    .text-wrapper h5 { margin: 0; font-weight: bold; font-size: 15px; }
    .text-wrapper small { font-size: 11px; opacity: 0.8; }
    
    .arrow-icon {
        position: absolute; left: 20px; opacity: 0; transition: 0.2s;
    }
    .action-box:hover .arrow-icon { opacity: 1; left: 15px; }

    .hover-scale:hover { transform: translateY(-2px); transition: 0.2s; }
    /* Hover Effect for the Button */
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(245, 158, 11, 0.3) !important; /* Yellow Glow */
    }

</style>

<script>
    function confirmAuto(slotId, name) {
        Swal.fire({
            title: 'توليد تلقائي: ' + name,
            html: "سيقوم النظام بتطبيق خوارزمية <b>Graph Coloring</b>.<br>سيتم مسح أي جدول سابق لهذه المجموعة.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#9333ea',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-wand-magic-sparkles"></i> ابدأ التوليد',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/schedules/auto/" + slotId;
            }
        });
    }
</script>
@endsection