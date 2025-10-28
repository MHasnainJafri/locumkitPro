<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerPrivateJobResource extends JsonResource
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
            'title' => $this->job_title,
            'location' => $this->job_location,
            'rate' =>  set_amount_format($this->job_rate),
            'company' => $this->emp_name,
            'startTime' => $this->job_date->format("m/d/Y 5:30:00"),
            'endTime' => $this->job_date->format("m/d/Y 17:30:00"),
            'allDay' => false,
            'job_type' => 'private'
        ];
    }
}