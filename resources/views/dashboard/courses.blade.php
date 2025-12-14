@extends('layouts.sidebar')
@section('title')
    إدارة المواد
@endsection
@section('body')
    <section class="content">
        <h2 class="section-title">إدارة المقررات</h2>
        <div class="controls">
            <form method="GET" action="{{ route('courses.index') }}" class="filter-group">
                <input type="text" name="search" value="{{ request('search') }}" style="width: 250px;"
                    placeholder="بحث باسم المادة أو الكود...">
                <button type="submit" class="btn add">
                    بحث
                </button>
            </form>

            <a href="#addSubjectModal" class="btn add">
                <i class="fa-solid fa-plus"></i> إضافة مادة جديدة
            </a>
        </div>
        @if ($message)
            <div id="autoAlert" class="alert alert-warning" style="margin-bottom:15px;">
                <i class="fa-solid fa-circle-info"></i>
                {{ $message }}
            </div>
        @endif

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>اسم المقرر</th>
                        <th>كود المادة</th>
                        <th>القسم التابع له</th>
                        <th>الفرقة</th>
                        <th>الساعات المعتمدة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        {{-- @foreach ($course->departments as $department) --}}
                            <tr>
                                <td>{{ $course->course_name }}</td>
                                <td>{{ $course->course_code }}</td>
                                {{-- <td>{{ $department->department_name }}</td> --}}
                                <td>{{ $course->departments->pluck('department_name')->implode('، ') }}</td>
                                <td>{{ $course->level_name }}</td>
                                <td>{{ $course->credit_hours_name }} </td>
                                <td>
                                    <a href="#editSubjectModal-{{ $course->id }}" class="small-btn edit"
                                        title="تعديل المادة"><i class="fa-solid fa-pen"></i></a>
                                    <a href="#deleteSubjectModal-{{ $course->id }}" class="small-btn del"
                                        title="حذف المادة"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        {{-- @endforeach --}}
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
@section('modals')
    {{-- مودل الاضافة --}}
    <div id="addSubjectModal" class="modal-overlay">
        <div class="modal">
            <h3>إضافة مقرر دراسي جديد</h3>

            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="col">
                        <label>اسم المادة</label>
                        <input type="text" name="course_name" value="{{ old('course_name') }}"
                            placeholder="مثال: هندسة برمجيات">
                        @error('course_name', 'addSubjectModal')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col">
                        <label>كود المادة</label>
                        <input type="text" name="course_code" value="{{ old('course_code') }}"
                            placeholder="مثال: CS305">
                        @error('course_code', 'addSubjectModal')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- الفرقة + الترم --}}
                <div class="form-row">
                    <div class="col">
                        <label>الفرقة الدراسية</label>
                        <select name="level" id="levelSelect" required>
                            <option value="">اختر الفرقة</option>
                            <option value="1" {{ old('level') == 1 ? 'selected' : '' }}>الفرقة الأولى</option>
                            <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>الفرقة الثانية</option>
                            <option value="3" {{ old('level') == 3 ? 'selected' : '' }}>الفرقة الثالثة</option>
                            <option value="4" {{ old('level') == 4 ? 'selected' : '' }}>الفرقة الرابعة</option>
                        </select>
                    </div>

                    <div class="col">
                        <label>الترم</label>
                        <select name="semester" required>
                            <option value="1" {{ old('semester') == 1 ? 'selected' : '' }}>الترم الأول</option>
                            <option value="2" {{ old('semester') == 2 ? 'selected' : '' }}>الترم الثاني</option>
                        </select>
                    </div>
                </div>

                {{-- الأقسام (تظهر فقط للثالثة والرابعة) --}}
                <div class="form-row" id="departmentWrapper">
                    <div class="col">
                        <label class="form-label">الأقسام الأكاديمية</label>

                        @php
                            $oldDepartments = old('department_ids', []);
                        @endphp

                        <div class="department-checkboxes">
                            <label class="checkbox-item">
                                <input type="checkbox" name="department_ids[]" value="2"
                                    {{ in_array(2, $oldDepartments) ? 'checked' : '' }}>
                                <span>علوم الحاسب (CS)</span>
                            </label>

                            <label class="checkbox-item">
                                <input type="checkbox" name="department_ids[]" value="3"
                                    {{ in_array(3, $oldDepartments) ? 'checked' : '' }}>
                                <span>نظم المعلومات (IS)</span>
                            </label>

                            <label class="checkbox-item">
                                <input type="checkbox" name="department_ids[]" value="4"
                                    {{ in_array(4, $oldDepartments) ? 'checked' : '' }}>
                                <span>الذكاء الاصطناعي (AI)</span>
                            </label>

                            <label class="checkbox-item">
                                <input type="checkbox" name="department_ids[]" value="5"
                                    {{ in_array(5, $oldDepartments) ? 'checked' : '' }}>
                                <span>تكنولوجيا المعلومات (IT)</span>
                            </label>
                        </div>

                        @error('department_ids', 'addSubjectModal')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="department_ids[]" value="1" id="generalDepartment">

                <div class="form-row">
                    <div class="col">
                        <label>عدد الساعات المعتمدة</label>
                        <input type="number" name="credit_hours" min="1" max="6"
                            value="{{ old('credit_hours', 3) }}">
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="{{ route('courses.index') }}" class="btn-secondary">إلغاء</a>
                    <button type="submit" class="btn add">حفظ المادة</button>
                </div>
            </form>
        </div>
    </div>


    @foreach ($courses as $course)
        {{-- مودل التعديل --}}
        <div id="editSubjectModal-{{ $course->id }}" class="modal-overlay">
            <div class="modal">
                <h3>
                    <i class="fa-solid fa-pen-to-square" style="color:var(--warning); margin-left:10px;"></i>
                    تعديل مقرر دراسي
                </h3>

                <form action="{{ route('courses.update', $course->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- اسم المادة + الكود --}}
                    <div class="form-row">
                        <div class="col">
                            <label>اسم المادة</label>
                            <input type="text" name="course_name"
                                value="{{ old('course_name', $course->course_name) }}" >
                             @error('course_name', 'editSubjectModal-'.$course->id)
                                    <span style="color:red;">{{ $message }}</span>
                             @enderror
                        </div>

                        <div class="col">
                            <label>كود المادة</label>
                            <input type="text" name="course_code"
                                value="{{ old('course_code', $course->course_code) }}" >
                             @error('course_code', 'editSubjectModal-'.$course->id)
                                <span style="color:red;">{{ $message }}</span>
                            @enderror

                        </div>
                    </div>

                    {{-- الفرقة + الترم --}}
                    <div class="form-row">
                        <div class="col">
                            <label>الفرقة الدراسية</label>
                            <select name="level" id="levelSelect-{{ $course->id }}" required>
                                <option value="1" {{ old('level', $course->level) == 1 ? 'selected' : '' }}>الفرقة الأولى
                                </option>
                                <option value="2" {{ old('level', $course->level) == 2 ? 'selected' : '' }}>الفرقة الثانية
                                </option>
                                <option value="3" {{ old('level', $course->level) == 3 ? 'selected' : '' }}>الفرقة الثالثة
                                </option>
                                <option value="4" {{ old('level', $course->level) == 4 ? 'selected' : '' }}>الفرقة الرابعة
                                </option>
                            </select>
                        </div>

                        <div class="col">
                            <label>الترم</label>
                            <select name="semester" required>
                                <option value="1" {{ old('semester', $course->semester) == 1 ? 'selected' : '' }}>الترم
                                    الأول</option>
                                <option value="2" {{ old('semester', $course->semester) == 2 ? 'selected' : '' }}>الترم
                                    الثاني</option>
                            </select>
                        </div>
                    </div>

                    {{-- الأقسام الأكاديمية (تظهر فقط للثالثة والرابعة) --}}
                    @php
                        $selectedDepartments = old('department_ids', $course->departments->pluck('id')->toArray());
                    @endphp

                    <div class="form-row" id="departmentWrapper-{{ $course->id }}">
                        <div class="col">
                            <label class="form-label">الأقسام الأكاديمية</label>

                            <div class="department-checkboxes">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="department_ids[]" value="2"
                                        {{ in_array(2, $selectedDepartments) ? 'checked' : '' }}>
                                    <span>علوم الحاسب (CS)</span>
                                </label>

                                <label class="checkbox-item">
                                    <input type="checkbox" name="department_ids[]" value="3"
                                        {{ in_array(3, $selectedDepartments) ? 'checked' : '' }}>
                                    <span>نظم المعلومات (IS)</span>
                                </label>

                                <label class="checkbox-item">
                                    <input type="checkbox" name="department_ids[]" value="4"
                                        {{ in_array(4, $selectedDepartments) ? 'checked' : '' }}>
                                    <span>الذكاء الاصطناعي (AI)</span>
                                </label>

                                <label class="checkbox-item">
                                    <input type="checkbox" name="department_ids[]" value="5"
                                        {{ in_array(5, $selectedDepartments) ? 'checked' : '' }}>
                                    <span>تكنولوجيا المعلومات (IT)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="department_ids[]" value="1"
                        id="generalDepartment-{{ $course->id }}">

                    <div class="form-row">
                        <div class="col">
                            <label>عدد الساعات المعتمدة</label>
                            <input type="number" name="credit_hours" min="1" max="6"
                                value="{{ old('credit_hours', $course->credit_hours) }}">
                        </div>
                    </div>

                    <div class="modal-actions">
                        <a href="{{ route('courses.index') }}" class="btn-secondary">إلغاء</a>
                        <button type="submit" class="btn add" style="background-color: var(--warning); color:#000;">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- مودل الحذف --}}
        <div id="deleteSubjectModal-{{ $course->id }}" class="modal-overlay">
            <div class="modal" style="width: 400px; text-align: center;">

                <div style="margin-bottom: 20px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 50px; color: var(--danger);"></i>
                </div>

                <h3 style="justify-content: center; border: none; margin-bottom: 10px;">هل أنت متأكد؟</h3>

                <p style="color: var(--muted); margin-bottom: 25px;">
                    أنت على وشك حذف المادة: <br>
                    <span style="color: #000; font-weight: bold;">
                        {{ $course->course_name }}
                        ({{ $course->course_code }})
                    </span> <br>
                    <span style="font-size: 13px; color: var(--danger);">سيؤدي هذا لحذفها من سجلات جميع الطلاب المسجلين
                        بها.</span>
                </p>

                <form action="{{ route('courses.destroy', $course->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-actions" style="justify-content: center;">
                        <a href="{{ route('courses.index') }}" class="btn-secondary">تراجع</a>
                        <button type="submit" class="btn" style="background-color: var(--danger); color: white;">
                            نعم، احذف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- فتح modals لو فى اخطاء --}}
    @if (session('open_modal'))
        <script>
            window.addEventListener('load', function() {
                window.location.hash = '#{{ session('open_modal') }}';
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 2000
            })
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function handleLevel(levelSelect, departmentWrapper, generalDepartment) {

                function toggle() {
                    const level = levelSelect.value;

                    if (level === '3' || level === '4') {
                        // فرقة ثالثة أو رابعة
                        departmentWrapper.style.display = 'block';
                        generalDepartment.disabled = true;
                    } else {
                        // فرقة أولى أو تانية
                        departmentWrapper.style.display = 'none';
                        generalDepartment.disabled = false;
                    }
                }

                levelSelect.addEventListener('change', toggle);
                toggle(); // تشغيل أول ما الصفحة تحمل
            }

            /* ========= مودل الإضافة ========= */
            const addLevel = document.getElementById('levelSelect');
            const addWrapper = document.getElementById('departmentWrapper');
            const addGeneral = document.getElementById('generalDepartment');

            if (addLevel && addWrapper && addGeneral) {
                handleLevel(addLevel, addWrapper, addGeneral);
            }

            /* ========= مودلات التعديل ========= */
            document.querySelectorAll('[id^="levelSelect-"]').forEach(select => {
                const id = select.id.replace('levelSelect-', '');

                const wrapper = document.getElementById('departmentWrapper-' + id);
                const general = document.getElementById('generalDepartment-' + id);

                if (wrapper && general) {
                    handleLevel(select, wrapper, general);
                }
            });

        });
    </script>

    <!-- إخفاء رسالة التنبيه تلقائيًا بعد عدة ثوانٍ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('autoAlert');

            if (alertBox) {
                setTimeout(() => {
                    alertBox.style.transition = 'opacity 0.5s ease';
                    alertBox.style.opacity = '0';

                    setTimeout(() => {
                        alertBox.remove();
                    }, 500);
                }, 3000); // 3 ثواني
            }
        });
    </script>
@endsection
