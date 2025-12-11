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
                    <tr>
                        <td>1</td>
                        <td>مدرج (أ)</td>
                        <td>مبنى كلية الحاسبات - الدور الأرضي</td>
                        <td>
                            <span
                                style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; border: 1px solid #bae6fd;">
                                150 طالب
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--success); font-weight: bold; font-size: 13px;">
                                <i class="fa-solid fa-circle-check"></i> متاحة للاستخدام
                            </span>
                        </td>
                        <td>
                            <a href="#editRoomModal" class="small-btn edit" title="تعديل البيانات">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="#deleteRoomModal" class="small-btn del" title="حذف القاعة">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </section>
@endsection
@section('modals')
    <div id="addRoomModal" class="modal-overlay">
        <div class="modal">
            <h3>إضافة قاعة جديدة</h3>
            <form action="/rooms/save" method="POST" class="styled-form">

                <div class="form-row">
                    <div class="col">
                        <label>اسم القاعة <span class="required">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-tag"></i>
                            <input type="text" name="room_name" required placeholder="مثال: مدرج ب">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>موقع القاعة</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-location-dot"></i>
                            <input type="text" name="location" placeholder="مثال: الدور الثالث">
                        </div>
                    </div>
                    <div class="col">
                        <label>السعة الاستيعابية (عدد الطلاب)</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-users"></i>
                            <input type="number" name="capacity" min="1" placeholder="مثال: 60">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>حالة القاعة</label>
                        <select name="status">
                            <option value="active">متاحة للاستخدام</option>
                            <option value="maintenance">تحت الصيانة</option>
                            <option value="closed">مغلقة</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <a href="#" class="btn-secondary">إلغاء</a>
                    <button type="submit" class="btn add">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editRoomModal" class="modal-overlay">
        <div class="modal">
            <h3>
                <i class="fa-solid fa-pen-to-square" style="color:var(--warning); margin-left:10px;"></i>
                تعديل بيانات القاعة
            </h3>
            <form action="/rooms/update" method="POST" class="styled-form">

                <input type="hidden" name="room_id" value="1">

                <div class="form-row">
                    <div class="col">
                        <label>اسم القاعة</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-tag"></i>
                            <input type="text" name="room_name" value="مدرج (أ)" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>موقع القاعة</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-location-dot"></i>
                            <input type="text" name="location" value="مبنى كلية الحاسبات - الدور الأرضي">
                        </div>
                    </div>
                    <div class="col">
                        <label>السعة الاستيعابية</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-users"></i>
                            <input type="number" name="capacity" value="150">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label>حالة القاعة</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-circle-check"></i>

                            <select name="status" style="padding-right: 40px;">
                                <option value="active" selected>متاحة للاستخدام</option>
                                <option value="maintenance">تحت الصيانة</option>
                                <option value="closed">مغلقة</option>
                            </select>

                        </div>
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

    <div id="deleteRoomModal" class="modal-overlay">
        <div class="modal" style="width: 400px; text-align: center;">

            <div style="margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 50px; color: var(--danger);"></i>
            </div>

            <h3 style="justify-content: center; border: none; margin-bottom: 10px;">هل أنت متأكد؟</h3>

            <p style="color: var(--muted); margin-bottom: 25px;">
                أنت على وشك حذف القاعة: <br>
                <span style="color: #000; font-weight: bold;">مدرج (أ)</span>
                <br>
                <span style="font-size: 13px; color: var(--danger);">
                    سيتم إزالتها من أي جداول امتحانات مرتبطة بها.
                </span>
            </p>

            <form action="/rooms/delete" method="POST">
                <input type="hidden" name="room_id" value="1">

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