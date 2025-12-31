@extends('layouts.sidebar')

@section('title', 'إدارة الطلاب | جامعة الغردقة')

@section('body')

    <main class="cintent">
        <section class="content">
            <h2 class="section-title">قائمة الطلاب المسجلين</h2>

            <form method="GET" action="{{ route('students.index') }}" class="controls">
                <div class="filter-group">
                    <input type="text" name="search" id="searchInput" style="width: 250px;"
                        value="{{ request('search') }}"
                        placeholder="بحث بالاسم أو البريد...">

                    <select name="year" id="yearFilter" style="width: 150px;" onchange="this.form.submit()">
                        <option value="">كل الفرق</option>
                        <option value="1" {{ request('year') == '1' ? 'selected' : '' }}>الفرقة الأولى</option>
                        <option value="2" {{ request('year') == '2' ? 'selected' : '' }}>الفرقة الثانية</option>
                        <option value="3" {{ request('year') == '3' ? 'selected' : '' }}>الفرقة الثالثة</option>
                        <option value="4" {{ request('year') == '4' ? 'selected' : '' }}>الفرقة الرابعة</option>
                    </select>
                </div>

                <a href="#addStudentModal" class="btn add">
                    <i class="fa-solid fa-plus"></i> إضافة طالب
                </a>
            </form>

            <div class="card">
                <table id="studentsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الرقم الجامعي</th>
                            <th>البريد الإلكتروني</th>
                            <th>الفرقة</th>
                            <th>القسم</th>
                            <th>عدد المواد</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            {{-- Fetch Name/Email from User Relationship --}}
                            <td>{{ $student->user->name ?? 'غير متوفر' }}</td>
                            <td>{{ $student->student_code ?? '---' }}</td>
                            <td>{{ $student->user->email ?? '---' }}</td>
                            <td>
                                @if($student->level == 1) الأولى
                                @elseif($student->level == 2) الثانية
                                @elseif($student->level == 3) الثالثة
                                @else الرابعة
                                @endif
                            </td>
                            <td>{{ $student->department->department_name ?? 'عام' }}</td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; border: 1px solid #bae6fd;">
                                        {{ $student->courses->count() }} مواد
                                    </span>
                                    {{-- Use @json to fix arrow mistake in JS --}}
                                    <button class="small-btn show" title="عرض المواد"
                                       onclick='openViewModal(@json($student), @json($student->courses), "{{ $student->user->name }}")'
                                       style="padding: 5px 8px; border:none; cursor:pointer;">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <a href="#editStudentModal" class="small-btn edit" title="تعديل البيانات"
                                   onclick='openEditModal(@json($student), "{{ $student->user->name }}", "{{ $student->user->email }}")'>
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <a href="#deleteStudentModal" class="small-btn del" title="حذف الطالب"
                                   onclick='openDeleteModal({{ $student->id }}, "{{ $student->user->name }}")'>
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
             
            </div>
        </section>
    </main>

    <div id="addStudentModal" class="modal-overlay">
        <div class="modal">
            <h3>إضافة طالب جديد</h3>

            <form id="studentForm" action="{{ route('students.store') }}" method="POST">
                @csrf
                <h4 style="font-size:14px; color:#1e3a8a; border-bottom:1px solid #eee; padding-bottom:5px; margin-bottom:10px;">
                    1. البيانات الأساسية
                </h4>

                <div class="form-row">
                    <div class="col">
                        <label for="f_name">الاسم الرباعي</label>
                        <input type="text" id="f_name" name="full_name" required placeholder="أدخل الاسم كاملاً">
                    </div>
                    <div class="col">
                        <label for="f_studentId">الرقم الجامعي</label>
                        <input type="number" id="f_studentId" name="student_code" placeholder="أدخل الرقم الجامعي">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="f_email">البريد الإلكتروني</label>
                        <input type="email" id="f_email" name="email" placeholder="student@hurghada.edu.eg">
                    </div>
                    <div class="col">
                         <label for="f_password">كلمة المرور</label>
                         <input type="password" id="f_password" name="password" required placeholder="******" minlength="8">
                    </div>
                </div>

                <div class="form-row">
                     <div class="col">
                        <label for="f_current_level">الفرقة المقيد بها الطالب</label>
                        <select id="f_current_level" name="current_level" required
                            style="background-color: #f0f9ff; border-color: #bae6fd;">
                            <option value="1">الفرقة الأولى</option>
                            <option value="2">الفرقة الثانية</option>
                            <option value="3">الفرقة الثالثة</option>
                            <option value="4">الفرقة الرابعة</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="f_main_dept">القسم الرئيسي للطالب</label>
                        <select id="f_main_dept" name="department_id" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <h4 style="font-size:14px; color:#1e3a8a; border-bottom:1px solid #eee; padding-bottom:5px; margin-top:20px; margin-bottom:10px;">
                    2. تسجيل المواد الدراسية
                </h4>

                <div class="filter-card" style="background:#fffbeb; border-color:#fde68a;">
                    <div class="filter-item">
                        <label for="search_dept" style="color:#b45309">بحث في مواد قسم:</label>
                        <div class="select-wrapper">
                            <select id="search_dept" onchange="filterChecklist()">
                                <option value="all">الكل</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="filter-item">
                        <label for="search_year" style="color:#b45309">بحث في مواد الفرقة:</label>
                        <div class="select-wrapper">
                            <select id="search_year" onchange="filterChecklist()">
                                <option value="all">الكل</option>
                                <option value="1">الأولى</option>
                                <option value="2">الثانية</option>
                                <option value="3">الثالثة</option>
                                <option value="4">الرابعة</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>قائمة المواد المتاحة (حدد للاختيار)</label>
                        <div id="f_subjects_checklist" class="subjects-checklist" style="max-height: 200px; overflow-y: auto;">
                            @foreach($allCourses as $course)
                                <div class="checkbox-item course-item" data-dept="{{ $course->departments->first()->id ?? 'all' }}" data-level="{{ $course->level }}">
                                    <input type="checkbox" name="subjects[]" value="{{ $course->id }}" id="c_{{ $course->id }}" onchange="updateSelectedList()">
                                    <label for="c_{{ $course->id }}">
                                        {{ $course->course_name }} ({{ $course->course_code }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="selected-subjects-area">
                    <label style="color:#15803d; font-weight:bold;">
                        <i class="fa-solid fa-check-circle"></i> المواد التي سيتم تسجيلها:
                    </label>

                    <div class="selected-list" id="selectedSubjectsList">
                        <p class="no-selection">لم يتم اختيار مواد بعد...</p>
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="#" class="btn-secondary" id="closeModalBtn" onclick="window.location.hash=''">إلغاء</a>
                    <button type="submit" class="btn add">حفظ البيانات</button>
                </div>

            </form>
        </div>
    </div>

    <div id="viewSubjectsModal" class="modal-overlay">
        <div class="modal" style="width: 700px;">
            <h3 style="border-bottom:none; margin-bottom:10px;">
                المواد المسجلة للطالب:
                <span style="color:var(--accent)" id="viewStudentName"></span>
            </h3>

            <div style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                <table style="margin:0;">
                    <thead style="background:#f1f5f9;">
                        <tr>
                            <th style="padding:10px; font-size:13px;">كود المادة</th>
                            <th style="padding:10px; font-size:13px;">اسم المقرر</th>
                            <th style="padding:10px; font-size:13px;">الفرقة الأصلية</th>
                            <th style="padding:10px; font-size:13px;">الساعات</th>
                        </tr>
                    </thead>
                    <tbody id="viewSubjectsBody">
                        </tbody>
                </table>
            </div>

            <div class="modal-actions">
                <a href="#" class="btn-close-modal" onclick="window.location.hash=''">
                    <i class="fa-solid fa-xmark"></i> إغلاق النافذة
                </a>
            </div>
        </div>
    </div>

    <div id="editStudentModal" class="modal-overlay">
        <div class="modal">
            <h3>
                <i class="fa-solid fa-user-pen" style="color:var(--warning); margin-left:10px;"></i>
                تعديل بيانات الطالب
            </h3>

            <form action="{{ route('students.update') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" id="edit_student_id">

                <h4 class="form-section-title">1. البيانات الأساسية</h4>

                <div class="form-row">
                    <div class="col">
                        <label>الاسم الرباعي</label>
                        <input type="text" name="full_name" id="edit_full_name" required>
                    </div>
                    <div class="col">
                        <label>الرقم الجامعي</label>
                        <input type="number" name="student_code" id="edit_student_code" readonly style="background-color: #eee;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" id="edit_email">
                    </div>
                    <div class="col">
                        <label>الفرقة</label>
                        <select name="current_level" id="edit_current_level" style="background-color: #f0f9ff;">
                            <option value="1">الفرقة الأولى</option>
                            <option value="2">الفرقة الثانية</option>
                            <option value="3">الفرقة الثالثة</option>
                            <option value="4">الفرقة الرابعة</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>القسم</label>
                        <select name="department_id" id="edit_department_id">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="#" class="btn-secondary" onclick="window.location.hash=''">إلغاء</a>
                    <button type="submit" class="btn add" style="background-color: var(--warning); color: #000;">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteStudentModal" class="modal-overlay">
        <div class="modal" style="width: 400px; text-align: center;">

            <div style="margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 50px; color: var(--danger);"></i>
            </div>

            <h3 style="justify-content: center; border: none; margin-bottom: 10px;">هل أنت متأكد؟</h3>

            <p style="color: var(--muted); margin-bottom: 25px;">
                أنت على وشك حذف الطالب: <br>
                <span style="color: #000; font-weight: bold;" id="delete_student_name_display"></span>
                <br>
                <span style="font-size: 13px; color: var(--danger);">لا يمكن التراجع عن هذا الإجراء.</span>
            </p>

            <form action="{{ route('students.destroy') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" id="delete_student_id">

                <div class="modal-actions" style="justify-content: center;">
                    <a href="#" class="btn-secondary" onclick="window.location.hash=''">تراجع</a>
                    <button type="submit" class="btn" style="background-color: var(--danger); color: white;">
                        نعم، احذف
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        function filterChecklist() {
            const dept = document.getElementById('search_dept').value;
            const year = document.getElementById('search_year').value;
            const items = document.querySelectorAll('.course-item');

            items.forEach(item => {
                const itemDept = item.getAttribute('data-dept');
                const itemLevel = item.getAttribute('data-level');
                
                // Logic: If selection is 'all' OR matches item
                const deptMatch = (dept === 'all' || itemDept == dept);
                const yearMatch = (year === 'all' || itemLevel == year);

                if (deptMatch && yearMatch) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function updateSelectedList() {
            const checkboxes = document.querySelectorAll('input[name="subjects[]"]:checked');
            const container = document.getElementById('selectedSubjectsList');
            
            if(checkboxes.length === 0) {
                container.innerHTML = '<p class="no-selection">لم يتم اختيار مواد بعد...</p>';
                return;
            }

            container.innerHTML = '';
            checkboxes.forEach(cb => {
                const labelText = cb.nextElementSibling.innerText;
                const div = document.createElement('div');
                div.style.cssText = "background: #f0f9ff; color: #0369a1; padding: 5px 10px; margin: 3px; border-radius: 5px; display: inline-block; font-size: 12px; border: 1px solid #bae6fd;";
                div.innerText = labelText;
                container.appendChild(div);
            });
        }

        function openViewModal(student, courses, studentName) {
            document.getElementById('viewStudentName').innerText = studentName;
            const tbody = document.getElementById('viewSubjectsBody');
            tbody.innerHTML = '';

            if (courses && courses.length > 0) {
                courses.forEach(c => {
                    tbody.innerHTML += `
                        <tr>
                            <td style="padding:10px;">${c.course_code}</td>
                            <td style="padding:10px;">${c.course_name}</td>
                            <td style="padding:10px;">
                                <span style="background:#e2e8f0; color:#475569; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:bold;">
                                    الفرقة ${c.level}
                                </span>
                            </td>
                            <td style="padding:10px;">${c.credit_hours}</td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:15px;">لا توجد مواد</td></tr>';
            }
            window.location.hash = 'viewSubjectsModal';
        }

        function openEditModal(student, userName, userEmail) {
            document.getElementById('edit_student_id').value = student.id;
            document.getElementById('edit_full_name').value = userName;
            document.getElementById('edit_student_code').value = student.student_code ?? ''; // Handle missing column
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_current_level').value = student.level;
            document.getElementById('edit_department_id').value = student.department_id;
            window.location.hash = 'editStudentModal';
        }

        function openDeleteModal(studentId, studentName) {
            document.getElementById('delete_student_id').value = studentId;
            document.getElementById('delete_student_name_display').innerText = studentName;
            window.location.hash = 'deleteStudentModal';
        }

        // Display Success Alert if session has 'success'
        @if(session('success'))
            Swal.fire({
                title: 'تم بنجاح',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'موافق'
            });
        @endif
    </script>

    <style>
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; }
        .modal-overlay:target { display: flex; }
        .checkbox-item { padding: 8px; border-bottom: 1px solid #eee; }
    </style>
@endsection