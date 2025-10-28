<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerStoreList extends Model
{
    use HasFactory;

    public function get_store_start_time($day = null): string|array
    {
        $start_time_value = "";
        if ($this->store_start_time && json_decode($this->store_start_time, true)) {
            $start_time = json_decode($this->store_start_time, true);
            if (is_null($day)) {
                return $start_time;
            }
            $start_time_value = isset($start_time[$day]) ? $start_time[$day] : "";
        }
        return $start_time_value;
    }
    public function get_store_end_time($day = null): string|array
    {
        $end_time_value = "";
        if ($this->store_end_time && json_decode($this->store_end_time, true)) {
            $end_time = json_decode($this->store_end_time, true);
            if (is_null($day)) {
                return $end_time;
            }
            $end_time_value = isset($end_time[$day]) ? $end_time[$day] : "";
        }
        return $end_time_value;
    }
    public function get_store_lunch_time($day = null): string|array
    {
        $lunch_time_value = "";
        if ($this->store_lunch_time && json_decode($this->store_lunch_time, true)) {
            $lunch_time = json_decode($this->store_lunch_time, true);
            if (is_null($day)) {
                return $lunch_time;
            }
            $lunch_time_value = isset($lunch_time[$day]) ? $lunch_time[$day] : "";
        }
        return $lunch_time_value;
    }
}
