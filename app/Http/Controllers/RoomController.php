<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;
        $message = null;

        $query = Room::query();

        if ($search) {
            $query->where('room_name', 'like', "%{$search}%");
        }

        $rooms = $query->get();

        if ($search && $rooms->isEmpty()) {
            $message = 'لا توجد قاعات مطابقة لبحثك';
            $rooms = Room::all();

        }

        return view('dashboard.rooms', compact('rooms', 'message', 'search'));
    }
    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'تم حذف القاعة بنجاح');
    }


    private function rules($id = null)
    {
        return [
            'room_name' => 'required|string|max:255|unique:rooms,room_name' . ($id ? ',' . $id : ''),
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string',
            'status' => 'required|in:1,2,3',
        ];
    }

    private function messages()
    {
        return [
            'room_name.required' => 'اسم القاعة مطلوب',
            'capacity.required' => 'السعة مطلوبة',
            'location.required' => 'الموقع مطلوب',
            'status.required' => 'حالة القاعة مطلوبة',
            'room_name.unique' => 'القاعة موجودة',
        ];
    }

    public function store(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            $this->rules(),
            $this->messages()
        );

        if ($validator->fails()) {
            return redirect()->route('rooms.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'addRoomModal');
        }

        Room::create([
            'room_name' => $request->room_name,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'status' => $request->status,
        ]);

        return redirect()->route('rooms.index')->with('success', 'تم إضافة القاعة بنجاح');
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->rules($room->id),
            $this->messages()
        );

        if ($validator->fails()) {
            return redirect()->route('rooms.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editRoomModal-' . $room->id);
        }

        $room->update([
            'room_name' => $request->room_name,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'status' => $request->status,
        ]);

        return redirect()->route('rooms.index')->with('success', 'تم تعديل بيانات القاعة بنجاح');
    }
}
