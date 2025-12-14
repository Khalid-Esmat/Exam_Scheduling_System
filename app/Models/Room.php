<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model{
  
   protected $fillable = [
    'room_name',
    'capacity',
    'location',
    'status',
    'is_available'
   ];

        public function getStatusNameAttribute()
    {
        return match ($this->status) {
            1 => 'متاحة للاستخدام',
            2 => 'تحت الصيانة',
            3 => 'مغلقة',
        };
    }
}

