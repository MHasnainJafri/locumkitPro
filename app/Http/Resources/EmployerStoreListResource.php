<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployerStoreListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $start_time = json_decode($this->store_start_time, true);
        $end_time = json_decode($this->store_end_time, true);
        $lunch_time = json_decode($this->store_lunch_time, true);
        return [
            "emp_st_id" => $this->id,
            "emp_id" => $this->employer_id,
            "emp_store_name" => $this->store_name,
            "emp_store_address" => $this->store_address,
            "emp_store_region" => $this->store_region,
            "emp_store_zip" => $this->store_zip,
            "store_start_time" => $start_time ? $start_time : null,
            "store_end_time" => $end_time ? $end_time : null,
            "store_lunch_time" => $lunch_time ? $lunch_time : null,
            "emp_language" => $this->language,
            "status" => $this->status,
            "date_added" => $this->created_at,
        ];
    }
}
