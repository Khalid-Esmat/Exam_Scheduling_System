<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller{
    
    public function __invoke(LoginRequest $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            // تحقق من الدور المختار = الدور في الداتابيز
            if (Auth::user()->role !== $request->role) {
                Auth::logout();
                return back()->withErrors([
                    'login_error' => 'اسم المستخدم أو كلمة المرور غير صحيحة.'
                ])->withInput();
            }

            // توجيه حسب الدور
            return match (Auth::user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'student' => redirect()->route('student.dashboard'),
                'supervisor' => redirect()->route('invigilator.dashboard'),
            };
        }

        return back()->withErrors([
            'login_error' => 'اسم المستخدم أو كلمة المرور غير صحيحة.'
        ])->withInput();


    }

}
