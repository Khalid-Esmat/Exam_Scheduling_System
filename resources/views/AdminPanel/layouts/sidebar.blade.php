<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | جامعة الغردقة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="Styles/index.css">
</head>

<body>

    <aside class="sidebar">
        <div>
            <div class="brand">
                <img src="Images/UNI_LOGO.png" alt="شعار الجامعة" class="brand-logo">
                <span>جامعة الغردقة</span>
            </div>

            <nav class="nav">
                <a href="#" data-section="dashboard">
                    <i class="fa-solid fa-palette"></i>
                    <span>لوحة التحكم</span>
                </a>
                <a href="students.html" data-section="students">
                    <i class="fa-solid fa-users"></i>
                    <span>الطلاب</span>
                </a>
                <a href="{{ route('courses.index') }}"  @class(['active' => request()->routeIs('courses.*')]) data-section="subjects">
                    <i class="fa-solid fa-book-open"></i>
                    <span>المواد الدراسية</span>
                </a>
                <a href="{{ route('invigilators.index') }}" @class(['active' => request()->routeIs('invigilators.*')]) data-section="observers">
                    <i class="fa-solid fa-user-check"></i>
                    <span>الملاحظون</span>
                </a>
                <a href="{{ route('rooms.index') }}" @class(['active' => request()->routeIs('rooms.*')]) data-section="rooms" >
                    <i class="fa-solid fa-door-closed"></i>
                    <span>القاعات</span>
                </a>
                <a href="#" data-section="add-schedule">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>إضافة جدول</span>
                </a>
                <a href="#" data-section="conflicts">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>التعارضات</span>
                </a>
            </nav>
        </div>

       <div class="logout">
            <form action="{{route('logout')}}" method="POST">
                @csrf
                <button type="submit">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    تسجيل الخروج
                </button>
            </form>
        </div>

    </aside>

    <main class="main">
        <div class="topbar">
            <h1>أهلاً،  {{auth()->user()->name}}</h1>
            <div class="user">
                <i class="fa-regular fa-user-circle"></i> Admin
            </div>
        </div>
        @yield('body')
    </main>
    @yield('modals')
</body>

</html>
