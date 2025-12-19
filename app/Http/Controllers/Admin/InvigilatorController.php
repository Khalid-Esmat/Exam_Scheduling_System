<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Invigilator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InvigilatorController extends Controller{

    /* =======================
        عرض كل الملاحظين + بحث
    ======================== */
    public function index(Request $request)
    {
        $search = $request->search;
        $message = null;

        $query = Invigilator::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'supervisor'));

        if ($search) {
            $filtered = (clone $query)
                ->where(function ($qq) use ($search) {
                    $qq->where('phone', 'like', "%$search%")
                       ->orWhere('job', 'like', "%$search%")
                       ->orWhereHas('user', function ($q) use ($search) {
                           $q->where('name', 'like', "%$search%")
                             ->orWhere('email', 'like', "%$search%");
                       });
                })
                ->get();

            if ($filtered->isEmpty()) {
                $invigilators = $query->get();
                $message = 'الملاحظ غير موجود';
            } else {
                $invigilators = $filtered;
            }
        } else {
            $invigilators = $query->get();
        }

        return view('AdminPanel.invigilators', compact('invigilators', 'message'));
    }

    /* =======================
        Validation Rules
    ======================== */
    private function rules($userId = null, $isUpdate = false)
    {
        return [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                $isUpdate
                    ? 'unique:users,email,' . $userId
                    : 'unique:users,email'
            ],

            'password' => $isUpdate
                ? 'nullable|string|min:6'
                : 'required|string|min:6',

            'phone' => 'required|string|size:11',
            'job'   => 'required|string|max:255',
        ];
    }

    /* =======================
        Validation Messages
    ======================== */
    private function messages()
    {
        return [
            'name.required' => 'اسم الملاحظ مطلوب.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email'    => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique'   => 'هذا البريد الإلكتروني مستخدم بالفعل.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min'      => 'كلمة المرور لا تقل عن 6 أحرف.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.size'     => 'رقم الهاتف يجب أن يكون 11 رقمًا.',

            'job.required' => 'الوظيفة مطلوبة.',
        ];
    }

    /* =======================
        إضافة ملاحظ
    ======================== */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->rules(),
            $this->messages()
        );

        if ($validator->fails()) {
            return redirect()->route('invigilators.index')
                ->withErrors($validator, 'addInvigilator')
                ->withInput()
                ->with('open_modal', 'addInvigilatorModal');
        }

        $data = $validator->validated();

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'supervisor',
            ]);

            Invigilator::create([
                'user_id' => $user->id,
                'phone'   => $data['phone'],
                'job'     => $data['job'],
            ]);
        });

        return redirect()->route('invigilators.index')
            ->with('success', 'تم إضافة الملاحظ بنجاح');
    }

    /* =======================
        تعديل ملاحظ
    ======================== */
    public function update(Request $request, Invigilator $invigilator)
    {
        $user = $invigilator->user;

        $validator = Validator::make(
            $request->all(),
            $this->rules($user->id, true),
            $this->messages()
        );

        if ($validator->fails()) {
            return redirect()->route('invigilators.index')
                ->withErrors($validator, 'editInvigilator')
                ->withInput()
                ->with('open_modal', 'editInvigilatorModal-' . $invigilator->id);
        }

        $data = $validator->validated();

        DB::transaction(function () use ($data, $user, $invigilator, $request) {

            $user->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $invigilator->update([
                'phone' => $data['phone'],
                'job'   => $data['job'],
            ]);
        });

        return redirect()->route('invigilators.index')
            ->with('success', 'تم تعديل بيانات الملاحظ بنجاح');
    }

    /* =======================
        حذف ملاحظ
    ======================== */
    public function destroy(Invigilator $invigilator)
    {
        DB::transaction(function () use ($invigilator) {
            $invigilator->user?->delete();
            $invigilator->delete();
        });

        return redirect()->route('invigilators.index')
            ->with('success', 'تم حذف الملاحظ بنجاح');
    }
}
