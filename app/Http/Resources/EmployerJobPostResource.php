<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployerJobPostResource extends JsonResource
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
            'locum' => $this->getAcceptedFreelancerData()["name"],
            'rate' =>  set_amount_format($this->job_rate),
            'startTime' => $this->job_date->format("m/d/Y 5:30:00"),
            'endTime' => $this->job_date->format("m/d/Y 17:30:00"),
            'allDay' => false,
            'job_type' => $this->getAcceptedFreelancerData()["type"]
        ];
    }
}