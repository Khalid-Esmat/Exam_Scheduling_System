@extends('layouts.sidebar')

@section('title', 'إضافة جدول امتحانات')

@section('body')
<div class="content">
    <h2 class="section-title">إعداد المجموعات والجدول الزمني</h2>

    {{-- تنبيهات النجاح عبر SweetAlert --}}
    @if(session('success'))
        <script>
            Swal.fire({
                title: 'تم بنجاح',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'موافق'
            });
        </script>
    @endif

    {{-- صندوق تنبيهات التحقق البرمجي (JS Validation) --}}
    <div id="validationAlert" class="card" style="display: none; background: #fee2e2; border-right: 4px solid var(--danger); padding: 15px; margin-bottom: 20px; color: #b91c1c;">
        <i class="fas fa-exclamation-triangle me-2"></i> <span id="alertText"></span>
    </div>

    <div class="form-row">
        <div class="col" style="flex: 0 0 300px;">
            <div class="card">
                <div class="card-header" style="padding: 15px; background: #f8fafc; border-bottom: 1px solid var(--border);">
                    <label class="fw-bold"><i class="fa-solid fa-filter"></i> المستويات المتاحة</label>
                    <input type="text" id="deptSearch" onkeyup="filterSidebar()" placeholder="بحث عن قسم..." style="margin-top:10px;">
                </div>
                <div class="sidebar-scroll" style="height: 500px; overflow-y: auto; padding: 10px;">
                    <div id="sidebarContainer">
                        @foreach($combinations as $combo)
                            <div class="checkbox-item" id="source-{{ $combo->id }}" 
                                 onclick="addLevel({{ $combo->id }}, '{{ $combo->department_name }}', '{{ $combo->level }}', {{ $combo->courses_count }})"
                                 style="margin-bottom: 8px; cursor: pointer;">
                                <div style="background: var(--primary); color: white; width: 30px; height: 30px; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                    {{ $combo->level }}
                                </div>
                                <div style="margin-right: 10px;">
                                    <div style="font-size: 13px; font-weight: bold;">{{ $combo->department_name }}</div>
                                    <small style="color: var(--muted)">{{ $combo->courses_count }} مادة</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <form id="mainForm" action="{{ route('examSlots.store') }}" method="POST" class="card" style="padding: 25px;">
                @csrf
                <div id="methodField"></div>
                
                <div class="controls">
                    <h3 id="formTitle" style="color: var(--primary); margin:0;">إنشاء مجموعة امتحانات جديدة</h3>
                    <button type="button" id="resetBtn" class="btn-secondary" style="display: none;" onclick="resetToCreateMode()">إلغاء التعديل</button>
                </div>

                <div class="form-row" style="margin-top: 20px;">
                    <div class="col">
                        <label>اسم المجموعة <span class="required">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-tag"></i>
                            <input type="text" name="slot_name" id="slot_name" placeholder="مثلاً: امتحانات الفصل الدراسي الأول" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>الأقسام والمستويات المختارة</label>
                        <div id="selectedBasket" class="selected-list" style="min-height: 80px; display: flex; flex-wrap: wrap; gap: 10px;">
                            <p class="no-selection">قم باختيار المستويات من القائمة اليمنى</p>
                        </div>
                        <div id="hiddenCombinationInputs"></div>
                    </div>
                </div>

                <div class="form-row" style="border-top: 1px solid var(--border); padding-top: 20px;">
                    <div class="col">
                        <label>تحديد أيام الامتحانات <span class="required">*</span></label>
                        <div class="filter-group">
                            <input type="date" id="datePicker" onchange="checkFestivalLive(this.value)" style="flex: 1;">
                            <button type="button" class="btn add" onclick="addDate()">إضافة يوم</button>
                        </div>
                        <div id="festivalWarning" style="color: var(--danger); font-size: 12px; margin-top: 5px; display: none;">⚠ هذا التاريخ يوافق عطلة رسمية!</div>
                        <div id="dateBasket" class="selected-list" style="margin-top: 10px; background: #f8fafc;"></div>
                        <div id="hiddenDateInputs"></div>
                    </div>

                    <div class="col" style="max-width: 300px;">
                        <label>الفترة الزمنية <span class="required">*</span></label>
                        <div class="form-row" style="gap: 10px;">
                            <div class="col">
                                <small>من</small>
                                <input type="time" name="start_time" id="start_time" onchange="renderUI()" required>
                            </div>
                            <div class="col">
                                <small>إلى</small>
                                <input type="time" name="end_time" id="end_time" onchange="renderUI()" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="submit" id="submitBtn" class="btn add" style="width: 200px; height: 50px; font-size: 16px;">حفظ المجموعة</button>
                </div>
            </form>
        </div>
    </div>

    <div class="content" style="margin-top: 40px;">
        <h2 class="section-title">المجموعات المفعلة حالياً</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
            @foreach($liveGroups as $group)
                <div class="card live-group-card" 
                     style="padding: 18px; border-top: 4px solid var(--sidebar); cursor: pointer; transition: all 0.3s ease; position: relative;"
                     onclick="window.location='{{ route('roomsAllocation.assign', $group->id) }}'">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="color: var(--sidebar); margin:0; font-size: 16px;">
                             <i class="fa-solid fa-folder-tree me-1"></i> {{ $group->slot_name }}
                        </h4>
                        <div style="display: flex; gap: 8px;">
                            {{-- أزرار التحكم مع منع Bubbling لمنع فتح الصفحة عند الضغط عليها --}}
                            <button class="small-btn edit" title="تعديل المجموعة" onclick='event.stopPropagation(); loadSlotForEdit(@json($group))'>
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('examSlots.destroy', $group->id) }}" method="POST" 
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة بالكامل؟')" 
                                  onclick="event.stopPropagation();">
                                @csrf @method('DELETE')
                                <button type="submit" class="small-btn del" title="حذف"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    
                    <div style="font-size: 12px; margin-bottom: 12px;">
                        <div style="color: var(--muted); margin-bottom: 5px; font-weight: bold;">الأقسام المشمولة:</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                            @foreach($group->members as $member)
                                <span class="selected-tag" style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; padding: 2px 8px; font-size: 10px;">
                                    {{ $member->department->department_name }} - م{{ $member->level }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div style="border-top: 1px solid #eee; padding-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 11px; color: var(--muted);">
                            <i class="fa-solid fa-clock me-1"></i> {{ $group->start_time }} - {{ $group->end_time }}
                        </span>
                        <span style="font-size: 11px; color: var(--accent); font-weight: bold;">
                            إدارة التوزيع <i class="fa-solid fa-arrow-left ms-1"></i>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

{{-- لا يظهر هذا القسم إلا إذا كانت قائمة الأقسام المتاحة فارغة --}}
@if($combinations->isEmpty())
    <div class="footer-actions" style="margin-top: 30px; padding: 20px; background: #fff; border-radius: 12px; border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
        <div class="text-muted">
            <i class="fa-solid fa-circle-check text-success"></i> تأكد من توزيع كافة الأقسام على القاعات.
        </div>
        <a href="{{ route('schedule.all') }}" class="btn add" style="background: var(--warning); color: #000;">
            الخطوة التالية: الجدولة الشاملة
            <i class="fa-solid fa-arrow-left ms-2"></i>
        </a>
    </div>
@else
    {{-- اختياري: يمكنك وضع رسالة تنبيه هنا تخبر المستخدم بضرورة توزيع باقي الأقسام --}}
    <div style="margin-top: 30px; text-align: center; color: var(--muted); font-size: 14px;">
        <i class="fa-solid fa-circle-info me-1"></i> يرجى توزيع كافة الأقسام المتبقية في القائمة اليمنى للمتابعة للخطوة التالية.
    </div>
@endif


</div>

{{-- الرسم التوضيحي لمسار العمل --}}


<script>
    const festivalDates = {!! json_encode($festivals->pluck('festival_date')->map->format('Y-m-d')) !!};
    let selectedLevels = [];
    let selectedDates = [];

    // دالة التعديل (Edit)
    function loadSlotForEdit(group) {
        resetToCreateMode(false);

        const form = document.getElementById('mainForm');
        const title = document.getElementById('formTitle');
        const resetBtn = document.getElementById('resetBtn');
        const methodField = document.getElementById('methodField');

        form.action = `/examSlots/${group.id}`;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        title.innerText = "تعديل المجموعة: " + group.slot_name;
        title.style.color = "var(--warning)";
        resetBtn.style.display = 'inline-block';

        document.getElementById('slot_name').value = group.slot_name;
        document.getElementById('start_time').value = group.start_time;
        document.getElementById('end_time').value = group.end_time;

        if(group.members) {
           group.members.forEach(m => {
           // 'm' IS the DepartmentLevel object directly
            const dl = m; 
        
           // Calculate course count from the eager-loaded department courses
          const cCount = dl.department && dl.department.courses ? 
                       dl.department.courses.filter(c => c.level == dl.level).length : 0;
        
          // Add to the UI basket
          addLevel(dl.id, dl.department.department_name, dl.level, cCount);
        });
        }

        if(group.exam_dates) {
            group.exam_dates.forEach(ed => {
                const dateOnly = ed.actual_date.split('T')[0];
                if(!selectedDates.includes(dateOnly)) selectedDates.push(dateOnly);
            });
        }

        renderUI();
        window.scrollTo({ top: 120, behavior: 'smooth' });
    }

    function resetToCreateMode(shouldReload = true) {
        if(shouldReload) { location.reload(); return; }
        
        selectedLevels.forEach(l => {
            const el = document.getElementById(`source-${l.id}`);
            if(el) { el.style.opacity = '1'; el.style.pointerEvents = 'auto'; }
        });

        selectedLevels = [];
        selectedDates = [];
        document.getElementById('mainForm').reset();
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('formTitle').innerText = "إنشاء مجموعة امتحانات جديدة";
        document.getElementById('formTitle').style.color = "var(--primary)";
        document.getElementById('resetBtn').style.display = 'none';
        renderUI();
    }

    function addLevel(id, name, level, count) {
        if (selectedLevels.some(l => l.id === id)) return;
        selectedLevels.push({ id, name, level, count });
        const el = document.getElementById(`source-${id}`);
        if(el) { el.style.opacity = '0.3'; el.style.pointerEvents = 'none'; }
        renderUI();
    }

    function removeLevel(id) {
        selectedLevels = selectedLevels.filter(l => l.id !== id);
        const source = document.getElementById(`source-${id}`);
        if(source) { source.style.opacity = '1'; source.style.pointerEvents = 'auto'; }
        renderUI();
    }

    function addDate() {
        const val = document.getElementById('datePicker').value;
        if (!val || selectedDates.includes(val)) return;
        if (festivalDates.includes(val)) { 
            Swal.fire({ title: 'تنبيه', text: 'هذا التاريخ يوافق عطلة رسمية', icon: 'warning' }); 
            return; 
        }
        selectedDates.push(val);
        document.getElementById('datePicker').value = '';
        renderUI();
    }

    function removeDate(date) {
        selectedDates = selectedDates.filter(d => d !== date);
        renderUI();
    }

    function renderUI() {
        const lBasket = document.getElementById('selectedBasket');
        const lInputs = document.getElementById('hiddenCombinationInputs');
        const dBasket = document.getElementById('dateBasket');
        const dInputs = document.getElementById('hiddenDateInputs');
        const submitBtn = document.getElementById('submitBtn');
        const alertBox = document.getElementById('validationAlert');
        const alertText = document.getElementById('alertText');

        lBasket.innerHTML = selectedLevels.length ? "" : '<p class="no-selection">قم باختيار المستويات من القائمة اليمنى</p>';
        lInputs.innerHTML = "";
        selectedLevels.forEach((l, i) => {
            lBasket.innerHTML += `<div class="selected-tag">${l.name} (م${l.level}) <button type="button" class="remove-tag" onclick="removeLevel(${l.id})">×</button></div>`;
            lInputs.innerHTML += `<input type="hidden" name="combinations[${i}][department_level_id]" value="${l.id}">`;
        });

        dBasket.innerHTML = selectedDates.length ? "" : '<p class="no-selection">لم يتم اختيار تواريخ</p>';
        dInputs.innerHTML = "";
        selectedDates.sort().forEach(d => {
            dBasket.innerHTML += `<div class="selected-tag" style="background:#fff; border:1px solid var(--sidebar); color:var(--sidebar)">${d} <button type="button" class="remove-tag" onclick="removeDate('${d}')">×</button></div>`;
            dInputs.innerHTML += `<input type="hidden" name="exam_dates[]" value="${d}">`;
        });

        let errors = [];
        const st = document.getElementById('start_time').value;
        const et = document.getElementById('end_time').value;
        if (st && et && st >= et) errors.push("وقت الانتهاء يجب أن يكون بعد وقت البدء.");

        if (selectedLevels.length > 0) {
            const maxC = Math.max(...selectedLevels.map(l => l.count));
            if (selectedDates.length > 0 && selectedDates.length < maxC) {
                errors.push(`تنبيه: أحد الأقسام يحتوي على ${maxC} مواد. يجب توفير عدد أيام مساوٍ على الأقل.`);
            }
        }

        if (errors.length > 0) {
            alertBox.style.display = 'block'; alertText.innerHTML = errors.join("<br>");
            submitBtn.disabled = true; submitBtn.style.opacity = '0.5';
        } else {
            alertBox.style.display = 'none'; submitBtn.disabled = false; submitBtn.style.opacity = '1';
        }
    }

    function filterSidebar() {
        const q = document.getElementById('deptSearch').value.toLowerCase();
        document.querySelectorAll('#sidebarContainer .checkbox-item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(q) ? 'flex' : 'none';
        });
    }

    function checkFestivalLive(val) {
        document.getElementById('festivalWarning').style.display = festivalDates.includes(val) ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', renderUI);
</script>

<style>
    .live-group-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        border-top-color: var(--warning) !important;
    }
    .selected-tag { transition: all 0.2s; }
    .selected-tag:hover { background: #fee2e2 !important; border-color: var(--danger) !important; color: var(--danger) !important; }
</style>
@endsection