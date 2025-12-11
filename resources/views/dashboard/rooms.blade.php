@extends('layouts.sidebar')
@section('title')
    إدارة القاعات
@endsection
@section('body')
    <section class="content">
        <h2 class="section-title">إدارة القاعات والمدرجات</h2>

        <div class="controls">
            <div class="filter-group">
                <input type="text" style="width: 250px;" placeholder="بحث باسم القاعة...">
            </div>

            <a href="#addRoomModal" class="btn add">
                <i class="fa-solid fa-plus"></i> إضافة قاعة
            </a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>رقم</th>
                        <th>اسم القاعة</th>
                        <th>موقع القاعة</th>
                        <th>السعة الاستيعابية</th>
                        <th>حالة القاعة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($rooms as $room)
                        <tr>

                            <td>{{ $loop->iteration }}</td>
                            <td> {{ $room->room_name }}</td>
                            <td>{{ $room->location }}</td>
                            <td>
                                <span
                                    style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; border: 1px solid #bae6fd;">
                                    {{ $room->capacity }} طالب
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--success); font-weight: bold; font-size: 13px;">
                                    @if ($room->is_available)
                                        <i class="fa-solid fa-circle-check"></i> متاحة للاستخدام
                                    @else
                                        يعقد فيها امتحان
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a href="#editRoomModal-{{ $room->id }}" class="small-btn edit" title="تعديل البيانات">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="#deleteRoomModal-{{ $room->id }}" class="small-btn del" title="حذف القاعة">
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
    <div id="addRoomModal" class="modal-overlay">
        <div class="modal">
            <h3>إضافة قاعة جديدة</h3>
            <form action="{{ route('rooms.store') }}" method="POST" class="styled-form">
                @csrf
                <div class="form-row">
                    <div class="col">
                        <label>اسم القاعة <span class="required">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-tag"></i>
                            <input type="text" name="room_name" value="{{ old('room_name') }}"
                                placeholder="مثال: مدرج ب">
                        </div>
                        @error('room_name')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>موقع القاعة</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-location-dot"></i>
                            <input type="text" name="location" value="{{ old('location') }}"
                                placeholder="مثال: الدور الثالث">
                        </div>
                        @error('location')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col">
                        <label>السعة الاستيعابية (عدد الطلاب)</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-users"></i>
                            <input type="number" name="capacity" min="1" value="{{ old('capacity') }}"
                                placeholder="مثال: 60">
                        </div>
                        @error('capacity')
                            <span style="color:red;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>حالة القاعة</label>
                        <select name="status">
                            <option value="1">متاحة للاستخدام</option>
                            <option value="0">يعقد فيها امتحان</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="{{ route('rooms.index') }}" class="btn-secondary">إلغاء</a>
                    <button type="submit" class="btn add">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
    {{-- فتح modals لو فى اخطاء --}}
    @if ($errors->any())
        <script>
            window.addEventListener('load', function() {
                window.location.hash = '#{{ session('open_modal') }}';
            });
        </script>
    @endif

    {{-- edit modal --}}
    @foreach ($rooms as $room)
        <div id="editRoomModal-{{ $room->id }}" class="modal-overlay">
            <div class="modal">
                <h3>
                    <i class="fa-solid fa-pen-to-square" style="color:var(--warning); margin-left:10px;"></i>
                    تعديل بيانات القاعة
                </h3>
                <form action="{{ route('rooms.update', $room->id) }}" method="POST" class="styled-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="room_id" value="1">

                    <div class="form-row">
                        <div class="col">
                            <label>اسم القاعة</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-tag"></i>
                                <input type="text" name="room_name" value= "{{ $room->room_name }}" required>
                            </div>
                        </div>
                    </div>
                    @error('room_name')
                        <span style="color:red;">{{ $message }}</span>
                    @enderror

                    <div class="form-row">
                        <div class="col">
                            <label>موقع القاعة</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-location-dot"></i>
                                <input type="text" name="location" value="{{ $room->location }}">
                            </div>
                        </div>
                        <div class="col">
                            <label>السعة الاستيعابية</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-users"></i>
                                <input type="number" name="capacity" value="{{ $room->capacity }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <label>حالة القاعة</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-circle-check"></i>

                                <select name="status" style="padding-right: 40px;">
                                    <option value="1" selected>متاحة للاستخدام</option>
                                    <option value="0">يعقد فيهاامتحان</option>
                                </select>

                            </div>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <a href="{{ route('rooms.index') }}" class="btn-secondary">إلغاء</a>
                        <button type="submit" class="btn add" style="background-color: var(--warning); color: #000;">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- delete modal --}}
        <div id="deleteRoomModal-{{ $room->id }}" class="modal-overlay">
            <div class="modal" style="width: 400px; text-align: center;">

                <div style="margin-bottom: 20px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 50px; color: var(--danger);"></i>
                </div>

                <h3 style="justify-content: center; border: none; margin-bottom: 10px;">هل أنت متأكد؟</h3>

                <p style="color: var(--muted); margin-bottom: 25px;">
                    أنت على وشك حذف القاعة: <br>
                    <span style="color: #000; font-weight: bold;">{{ $room->room_name }}</span>
                    <br>
                    <span style="font-size: 13px; color: var(--danger);">
                        سيتم إزالتها من أي جداول امتحانات مرتبطة بها.
                    </span>
                </p>

                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-actions" style="justify-content: center;">
                        <a href="{{ route('rooms.index') }}" class="btn-secondary">تراجع</a>
                        <button type="submit" class="btn" style="background-color: var(--danger); color: white;">
                            نعم، احذف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- رسالة اضافة وتعديل القاعة --}}
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
@endsection
