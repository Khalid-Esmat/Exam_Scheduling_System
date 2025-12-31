@extends('layouts.sidebar')
@section('title', 'توزيع القاعات')

@section('body')
<div class="content">
    <div class="topbar" style="margin-bottom: 25px; border-bottom: none; box-shadow: none; background: transparent; padding: 0;">
        <h2 class="section-title">توزيع اللجان: <span style="color: var(--sidebar)">{{ $slot->slot_name }}</span></h2>
    </div>

    {{-- System Alerts --}}
    @if(session('success'))
        <script>Swal.fire('نجاح', "{{ session('success') }}", 'success');</script>
    @endif
    @if(session('error'))
        <script>Swal.fire('خطأ', "{{ session('error') }}", 'error');</script>
    @endif

    <div class="form-row" style="align-items: flex-start; display: flex; gap: 20px;">
        
        {{-- Right Side: Distribution Tasks (Width: Flex 2) --}}
        <div class="col" style="flex: 2;">
            @foreach($slot->members as $member)
                @php
                    // $member is a DepartmentLevel object
                    $isFinished = $member->remaining <= 0;
                    $cardColor = $isFinished ? '#f0fdf4' : '#fff';
                    $borderColor = $isFinished ? 'var(--accent)' : 'var(--warning)';
                @endphp

                <div class="card mb-3" style="padding: 20px; margin-bottom: 15px; background: {{ $cardColor }}; border-right: 5px solid {{ $borderColor }};">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0; font-size: 18px; color: var(--primary);">
                                <i class="fa-solid fa-graduation-cap"></i> 
                                {{-- Direct access to department name and level --}}
                                {{ $member->department->department_name }} - م{{ $member->level }}
                            </h3>
                            <div style="font-size: 13px; color: var(--muted); margin-top: 5px;">
                                طلاب الدفعة: <strong>{{ $member->student_count }}</strong> طالب
                            </div>
                        </div>

                        <div>
                            @if($isFinished)
                                <span style="background: var(--accent); color: #fff; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px;">مكتمل</span>
                            @else
                                <span style="background: var(--warning); color: #000; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px;">متبقي: {{ $member->remaining }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Show currently allocated rooms for this level --}}
                    @php
                        // Correct PHP comment style used here to prevent ParseError
                        $allocations = \App\Models\ExamRoomAllocation::with('room')
                            ->where('exam_slot_id', $slot->id)
                            ->where('department_level_id', $member->id) // Using $member->id correctly
                            ->get();
                    @endphp

                    @if($allocations->count() > 0)
                        <div style="background: rgba(255,255,255,0.7); border: 1px solid var(--border); border-radius: 8px; padding: 10px; margin-bottom: 15px;">
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                @foreach($allocations as $alloc)
                                    <span class="selected-tag" style="background: #fff; border: 1px solid var(--border); padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                                        {{ $alloc->room->room_name }} ({{ $alloc->allocated_students }})
                                        <a href="{{ route('roomsAllocation.destroy', $alloc->id) }}" class="remove-tag" style="color: red; margin-right: 5px; text-decoration: none;" onclick="return confirm('إلغاء توزيع هذه القاعة؟')">×</a>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Allocation Form --}}
                    @if($member->remaining > 0)
                        <form action="{{ route('roomsAllocation.store', $slot->id) }}" method="POST" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed var(--border);">
                            @csrf
                            {{-- Correct ID used here --}}
                            <input type="hidden" name="department_level_id" value="{{ $member->id }}"> 
                            
                            <div style="display: flex; gap: 10px;">
                                <select name="allocations[0][room_id]" class="form-control" required style="flex: 2;">
                                    <option value="">-- اختر القاعة --</option>
                                    @foreach($rooms as $room)
                                        @php
                                            $used = \App\Models\ExamRoomAllocation::where('exam_slot_id', $slot->id)
                                                ->where('room_id', $room->id)
                                                ->sum('allocated_students');
                                            $free = $room->capacity - $used;
                                        @endphp
                                        @if($free > 0)
                                            <option value="{{ $room->id }}">
                                                {{ $room->room_name }} (متاح: {{ $free }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type="number" name="allocations[0][count]" class="form-control" placeholder="العدد" max="{{ $member->remaining }}" min="1" required style="flex: 1;">
                                <button type="submit" class="btn add" style="background: var(--sidebar); color: white; padding: 0 20px; border-radius: 5px;">توزيع</button>
                            </div>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Left Side: Sidebar Monitor (Width: Fixed 350px) --}}
        <div class="col" style="flex: 0 0 350px;">
            <div class="card" style="padding: 20px; position: sticky; top: 20px;">
                <h4 style="color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    <i class="fa-solid fa-chart-pie me-2"></i> إشغال القاعات
                </h4>
                
                <div class="sidebar-scroll" style="max-height: 70vh; overflow-y: auto;">
                    @foreach($rooms as $room)
                        @php
                            $used = \App\Models\ExamRoomAllocation::where('exam_slot_id', $slot->id)
                                ->where('room_id', $room->id)
                                ->sum('allocated_students');
                            $percent = ($room->capacity > 0) ? ($used / $room->capacity) * 100 : 0;
                            $color = $percent >= 100 ? 'var(--danger)' : ($percent > 80 ? 'var(--warning)' : 'var(--accent)');
                        @endphp

                        <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold; margin-bottom: 8px;">
                                <span>{{ $room->room_name }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span id="cap-text-{{ $room->id }}" style="color: {{ $percent >= 100 ? 'var(--danger)' : 'var(--muted)' }}">
                                        {{ $used }} / <span class="capacity-val">{{ $room->capacity }}</span>
                                    </span>
                                    <button onclick="showEditInput({{ $room->id }})" style="background:none; border:none; color:var(--accent); cursor:pointer; font-size: 10px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="edit-box-{{ $room->id }}" style="display: none; margin-bottom: 10px;">
                                <div class="input-group" style="display: flex; gap: 5px;">
                                    <input type="number" id="input-{{ $room->id }}" class="form-control form-control-sm" value="{{ $room->capacity }}">
                                    <button class="btn btn-sm btn-success" onclick="updateCapacity({{ $room->id }}, {{ $used }})" style="background: #10b981; color: white; border: none; padding: 5px 10px; border-radius: 4px;">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div style="background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div id="bar-{{ $room->id }}" style="width: {{ min($percent, 100) }}%; background: {{ $color }}; height: 100%; transition: 0.3s;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showEditInput(id) {
        const box = document.getElementById(`edit-box-${id}`);
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }

    function updateCapacity(id, used) {
        const newCap = document.getElementById(`input-${id}`).value;
        fetch(`/rooms/${id}/quick-update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ capacity: newCap })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`#cap-text-${id} .capacity-val`).innerText = data.new_capacity;
                const newPercent = (used / data.new_capacity) * 100;
                const bar = document.getElementById(`bar-${id}`);
                bar.style.width = Math.min(newPercent, 100) + '%';
                
                if(newPercent >= 100) bar.style.background = 'var(--danger)';
                else if(newPercent > 80) bar.style.background = 'var(--warning)';
                else bar.style.background = 'var(--accent)';
                
                showEditInput(id); // Close input
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم تحديث السعة',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
</script>
@endsection