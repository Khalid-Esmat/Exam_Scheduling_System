@extends('layouts.sidebar')
@section('title')
    إدارة المواد
@endsection
@section('body')
    <section class="content">
        <h2 class="section-title">إدارة المقررات</h2>
        <div class="controls">
            <div class="filter-group">
                <input type="text" style="width: 250px;" placeholder="بحث باسم المادة أو الكود...">
                <select style="width: 150px;">
                    <option value="">كل الأقسام</option>
                    <option value="ge">عام</option>
                    <option value="cs">علوم حاسب</option>
                    <option value="is">نظم معلومات</option>
                    <option value="ai">الذكاء الاصطناعي</option>
                    <option value="it">تكنولوجيا المعلومات</option>
                </select>
            </div>

            <a href="#addSubjectModal" class="btn add">
                <i class="fa-solid fa-plus"></i> إضافة مادة جديدة
            </a>
        </div>

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
                  @foreach ($courses as $course )
                    <tr>
                        <td>{{$course->course_name }}</td>
                        <td>{{$course->course_code }}</td>
                        <td>{{ $course->departments->pluck('department_name')->implode('، ') }}</td>
                        <td>{{$course ->level_name}}</td>
                        <td>{{$course->credit_hours_name }} </td>
                        <td>
                            <a href="#editSubjectModal" class="small-btn edit" title="تعديل المادة"><i
                                    class="fa-solid fa-pen"></i></a>
                            <a href="#deleteSubjectModal" class="small-btn del" title="حذف المادة"><i
                                    class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                 @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
@section('modals')
    <div id="addSubjectModal" class="modal-overlay">
        <div class="modal">
            <h3>إضافة مقرر دراسي جديد</h3>

            <form action="/subjects/save" method="POST">

                <div class="form-row">
                    <div class="col">
                        <label>اسم المادة</label>
                        <input type="text" name="subject_name" required placeholder="مثال: هندسة برمجيات">
                    </div>
                    <div class="col">
                        <label>كود المادة</label>
                        <input type="text" name="subject_code" required placeholder="مثال: CS305">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>القسم الأكاديمي</label>
                        <select name="department_id" required>
                            <option value="">اختر القسم...</option>
                            <option value="1">عام</option>
                            <option value="2">علوم الحاسب (CS)</option>
                            <option value="3">نظم المعلومات (IS)</option>
                            <option value="4">الذكاء الاصطناعي (AI)</option>
                            <option value="5">تكنولوجيا المعلومات (IT)</option>
                        </select>
                    </div>
                    <div class="col">
                        <label>الفرقة الدراسية</label>
                        <select name="academic_year" required>
                            <option value="1">الفرقة الأولى</option>
                            <option value="2">الفرقة الثانية</option>
                            <option value="3">الفرقة الثالثة</option>
                            <option value="4">الفرقة الرابعة</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>عدد الساعات المعتمدة</label>
                        <input type="number" name="credit_hours" min="1" max="6" value="3">
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="#" class="btn-secondary">إلغاء</a>
                    <button type="submit" class="btn add">حفظ المادة</button>
                </div>

            </form>
        </div>
    </div>

    <div id="editSubjectModal" class="modal-overlay">
        <div class="modal">
            <h3>
                <i class="fa-solid fa-pen-to-square" style="color:var(--warning); margin-left:10px;"></i>
                تعديل مقرر دراسي
            </h3>

            <form action="/subjects/update" method="POST">
                <input type="hidden" name="subject_id" value="101">

                <div class="form-row">
                    <div class="col">
                        <label>اسم المادة</label>
                        <input type="text" name="subject_name" value="هياكل البيانات" required>
                    </div>
                    <div class="col">
                        <label>كود المادة</label>
                        <input type="text" name="subject_code" value="CS201" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>القسم الأكاديمي</label>
                        <select name="department_id" required>
                            <option value="1">عام</option>
                            <option value="2" selected>علوم الحاسب (CS)</option>
                            <option value="3">نظم المعلومات (IS)</option>
                            <option value="4">الذكاء الاصطناعي (AI)</option>
                            <option value="5">تكنولوجيا المعلومات (IT)</option>
                        </select>
                    </div>
                    <div class="col">
                        <label>الفرقة الدراسية</label>
                        <select name="academic_year" required>
                            <option value="1">الفرقة الأولى</option>
                            <option value="2" selected>الفرقة الثانية</option>
                            <option value="3">الفرقة الثالثة</option>
                            <option value="4">الفرقة الرابعة</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>عدد الساعات المعتمدة</label>
                        <input type="number" name="credit_hours" min="1" max="6" value="3">
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="#" class="btn-secondary">إلغاء</a>
                    <button type="submit" class="btn add" style="background-color: var(--warning); color: #000;">
                        حفظ التعديلات
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div id="deleteSubjectModal" class="modal-overlay">
        <div class="modal" style="width: 400px; text-align: center;">

            <div style="margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 50px; color: var(--danger);"></i>
            </div>

            <h3 style="justify-content: center; border: none; margin-bottom: 10px;">هل أنت متأكد؟</h3>

            <p style="color: var(--muted); margin-bottom: 25px;">
                أنت على وشك حذف المادة: <br>
                <span style="color: #000; font-weight: bold;">(CS201) هياكل البيانات</span>
                <br>
                <span style="font-size: 13px; color: var(--danger);">سيؤدي هذا لحذفها من سجلات جميع الطلاب المسجلين
                    بها.</span>
            </p>

            <form action="/subjects/delete" method="POST">
                <input type="hidden" name="subject_id" value="101">

                <div class="modal-actions" style="justify-content: center;">
                    <a href="#" class="btn-secondary">تراجع</a>
                    <button type="submit" class="btn" style="background-color: var(--danger); color: white;">
                        نعم، احذف
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
