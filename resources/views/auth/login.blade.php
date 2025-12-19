<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | جامعة الغردقة</title>

    <link rel="stylesheet" href="{{ asset('Styles/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    @if ($errors->has('login_error'))
        <div id="errorPopup" class="error-popup">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first('login_error') }}</span>
        </div>
    @endif

    <div class="background-mesh"></div>

    <div class="login-container">

        <div class="brand-section">
            <div class="logo-circle">
                <img src="{{ asset('Images/UNI_LOGO.png') }}" alt="شعار الجامعة">
            </div>
            <h1>جامعة الغردقة</h1>
            <p class="subtitle">نظام الامتحانات والمراقبة</p>
        </div>
        <form action="{{ route('login') }}" method="POST" class="login-form">
            @csrf

            {{-- Role tabs --}}
            <div class="role-tabs">
                <input type="radio" id="admin" name="role" value="admin"
                    {{ old('role', 'admin') == 'admin' ? 'checked' : '' }}>
                <label for="admin">أدمن</label>

                <input type="radio" id="student" name="role" value="student"
                    {{ old('role') == 'student' ? 'checked' : '' }}>
                <label for="student">طالب</label>

                <input type="radio" id="proctor" name="role" value="supervisor"
                    {{ old('role') == 'supervisor' ? 'checked' : '' }}>
                <label for="proctor"> ملاحظ / مراقب </label>

                <div class="slider"></div>
            </div>

            {{-- Username --}}
            <div class="input-group">
                <div class="field">
                    <input type="text" name="email" id="username" placeholder=" " value="{{ old('email') }}">
                    <label for="username"> الايميل الجامعي</label>
                    <i class="fa-regular fa-user input-icon"></i>
                </div>
                @error('email')
                    <span style="color:red;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="input-group">
                <div class="field">
                    <input type="password" name="password" id="password" placeholder=" ">
                    <label for="password">كلمة المرور</label>
                    <i class="fa-regular fa-eye-slash toggle-pass" onclick="togglePass()"></i>
                </div>
                @error('password')
                    <span style="color:red;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Forgot link --}}
            {{-- <div class="actions">
               <a href="{{ route('login.forgotPassword') }}">نسيت كلمة المرور؟</a>
            </div> --}}

            {{-- Submit --}}
            <button type="submit" class="btn-submit" onclick="this.disabled=true; this.form.submit();">
                تسجيل الدخول <i class="fa-solid fa-arrow-left-long"></i>
            </button>

        </form>
    </div>

    <script>
        // toggle password show/hide
        function togglePass() {
            const passInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-pass');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                passInput.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');

            }
        }
    </script>

</body>

</html>
