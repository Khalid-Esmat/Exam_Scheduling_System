@extends('AdminPanel.layouts.sidebar')
@section('title')
    إدارة الملاحظين
@endsection
@section('body')
    <section class="content">
        <h2 class="section-title">قائمة الملاحظين</h2>

        <div class="controls"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">

            <!-- البحث -->
            <form method="GET" action="{{ route('invigilators.index') }}" style="display:flex; gap:10px; align-items:center;">

                <input type="text" name="search" placeholder="بحث بالاسم أو الإيميل أو الهاتف أو الوظيفة." value="{{ request('search') }}"
                    class="search-input" style="width:250px;">

                <button type="submit" class="btn add">
                    <i class="fa-solid fa-magnifying-glass"></i> بحث
                </button>
            </form>

            <!-- إضافة ملاحظ -->
            <a href="#addInvigilatorModal" class="btn add">
                <i class="fa-solid fa-plus"></i> إضافة ملاحظ
            </a>

        </div>
        @if ($message)
            <div id="autoAlert" class="alert alert-warning" style="margin-bottom:15px;">
                <i class="fa-solid fa-circle-info"></i>
                {{ $message }}
            </div>
        @endif


        <div class="card">
            <table id="observersTable">
                <thead>
                    <tr>
                        <th>رقم</th>
                        <th>الاسم</th>
                        <th>الوظيفة</th>
                        <th>رقم الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invigilators as $inv)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $inv->user->name }}</td>
                            <td>{{ $inv->job }}</td>
                            <td>{{ $inv->phone }}</td>
                            <td>{{ $inv->user->email }}</td>
                            <td>
                                <a href="#editInvigilatorModal-{{ $inv->id }}" class="small-btn edit"
                                    title="تعديل البيانات">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="#deleteInvigilatorModal-{{ $inv->id }}" class="small-btn del"
                                    title="حذف الملاحظ">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </section>
@endsection
@section('modals')
    <div id="addInvigilatorModal" class="modal-overlay">
        <div class="modal">
            <h3>إضافة ملاحظ جديد</h3>
            <form action="{{ route('invigilators.store') }}" method="POST" class="styled-form">
                @csrf
                <h4 class="form-section-title"><i class="fa-solid fa-user-gear"></i> البيانات والحساب</h4>

                <div class="form-row">
                    <div class="col" style="flex: 1.5;">
                        <label>الاسم الرباعي <span class="required">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="name" placeholder="أدخل الاسم بالكامل"
                                value="{{ session('open_modal') === 'addInvigilatorModal' ? old('name') : '' }}">

                        </div>
                        @error('name', 'addInvigilator')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col" style="flex: 1;">
                        <label>الوظيفة</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-briefcase"></i>
                            <input type="text" name="job" placeholder="مثال: معيد"
                                value="{{ session('open_modal') === 'addInvigilatorModal' ? old('job') : '' }}">
                        </div>
                        @error('job', 'addInvigilator')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>كلمة المرور</label>
                        <div class="input-icon-wrapper">
                            <input type="password" name="password" id="modal_password" placeholder="******"
                                value="{{ session('open_modal') === 'addInvigilatorModal' ? old('password') : '' }}">
                            <i class="fa-solid fa-eye toggle-pass-btn" onclick="togglePassword('modal_password', this)"></i>
                        </div>
                        @error('password', 'addInvigilator')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col">
                        <label>رقم الهاتف</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-phone-flip"></i>
                            <input type="tel" name="phone" placeholder="010xxxxxxx"
                                value="{{ session('open_modal') === 'addInvigilatorModal' ? old('phone') : '' }}">
                        </div>
                        @error('phone', 'addInvigilator')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>البريد الإلكتروني</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" placeholder="example@hurghada.edu.eg"
                                value="{{ session('open_modal') === 'addInvigilatorModal' ? old('email') : '' }}">
                        </div>
                        @error('email', 'addInvigilator')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="{{ route('invigilators.index') }}" class="btn-secondary">إلغاء</a>
                    <button type="submit" class="btn add">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($invigilators as $inv)
        {{-- edit modal --}}
        <div id="editInvigilatorModal-{{ $inv->id }}" class="modal-overlay">
            <div class="modal">
                <h3>
                    <i class="fa-solid fa-user-pen" style="color:var(--warning); margin-left:10px;"></i>
                    تعديل بيانات الم
                </h3>
                <form action="{{ route('invigilators.update', $inv->id) }}" method="POST" class="styled-form">
                    @csrf
                    @method('PUT')
                    <h4 class="form-section-title"><i class="fa-solid fa-user-gear"></i> البيانات والحساب</h4>

                    <div class="form-row">
                        <div class="col" style="flex: 1.5;">
                            <label>الاسم الرباعي <span class="required">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" name="name" value="{{ $inv->user->name }}" required>
                            </div>
                        </div>

                        <div class="col" style="flex: 1;">
                            <label>الوظيفة</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-briefcase"></i>
                                <input type="text" name="job" value="{{ $inv->job }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <label>كلمة المرور (اتركها فارغة لعدم التغيير)</label>
                            <div class="input-icon-wrapper">
                                <input type="password" name="password" id="modal_edit_password_{{ $inv->id }}"
                                    placeholder="***********">
                                <i class="fa-solid fa-eye toggle-pass-btn"
                                    onclick="togglePassword('modal_edit_password_{{ $inv->id }}', this)"></i>
                            </div>
                            @error('password', 'editInvigilator')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col">
                            <label>رقم الهاتف</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-phone-flip"></i>
                                <input type="tel" name="phone" value="{{ $inv->phone }}">
                            </div>
                            @error('phone', 'editInvigilator')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <label>البريد الإلكتروني</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" name="email" value="{{ $inv->user->email }}">
                                @error('email', 'editInvigilator')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <a href="{{ route('invigilators.index') }}" class="btn-secondary">إلغاء</a>
                        <button type="submit" class="btn add" style="background-color: var(--warning); color: #000;">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- delete modal --}}
        <div id="deleteInvigilatorModal-{{ $inv->id }}" class="modal-overlay">
            <div class="modal" style="width: 400px; text-align: center;">

                <div style="margin-bottom: 20px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 50px; color: var(--danger);"></i>
                </div>

                <h3 style="justify-content: center; border: none; margin-bottom: 10px;">هل أنت متأكد؟</h3>

                <p style="color: var(--muted); margin-bottom: 25px;">
                    أنت على وشك حذف الملاحظ: <br>
                    <span style="color: #000; font-weight: bold;">{{ $inv->user->name }}</span>
                    <br>
                    <span style="font-size: 13px; color: var(--danger);">سيتم حذف جدول الملاحظة الخاص به أيضاً.</span>
                </p>

                <form action="{{ route('invigilators.destroy', $inv->id) }}" method="POST">
                    @csrf
                    @method('DELETE') <div class="modal-actions" style="justify-content: center;">
                        <a href="{{ route('invigilators.index') }}" class="btn-secondary">تراجع</a>
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



    {{-- رسالة اضافة وتعديل ملاحظين --}}
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
        // دالة إظهار/إخفاء كلمة المرور
        function togglePassword(inputId, iconElement) {
            const input = document.getElementById(inputId);

            if (input.type === "password") {
                input.type = "text";
                iconElement.classList.remove("fa-eye");
                iconElement.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                iconElement.classList.remove("fa-eye-slash");
                iconElement.classList.add("fa-eye");
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const tableEyes = document.querySelectorAll(".table-password-wrapper i");

            tableEyes.forEach(icon => {
                icon.addEventListener("click", function() {
                    const input = this.previousElementSibling;
                    if (input.type === "password") {
                        input.type = "text";
                        this.classList.remove("fa-eye");
                        this.classList.add("fa-eye-slash");
                    } else {
                        input.type = "password";
                        this.classList.remove("fa-eye-slash");
                        this.classList.add("fa-eye");
                    }
                });
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
                }, 2000); // 3 ثواني
            }
        });
    </script>
@endsection
