<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class JobPostCurrentMonthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'date' => get_date_with_default_format($this->job_date),
            'day' => $this->job_date->format("D"),
            'rate' => set_amount_format($this->job_rate),
            'store' => $this->job_store->store_name,
            'location' => $this->job_store->store_address,
        ];
    }
}