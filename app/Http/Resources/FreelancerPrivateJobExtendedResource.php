<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerPrivateJobExtendedResource extends JsonResource
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
            "pv_id" => $this->id,
            "f_id" => $this->freelancer_id,
            "emp_name" => $this->emp_name,
            "emp_email" => $this->emp_email,
            "priv_job_title" => $this->job_title,
            "priv_job_rate" => $this->job_rate,
            "priv_job_location" => $this->job_location,
            "priv_job_start_date" => get_date_with_default_format($this->job_date),
            "priv_create_date" => $this->created_at->toDateTimeString(),
            "priv_update_date" => $this->updated_at->toDateTimeString(),
            "status" => $this->status,
        ];
    }
}