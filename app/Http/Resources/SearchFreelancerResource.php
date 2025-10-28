<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchFreelancerResource extends JsonResource
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
            "user_id" => $this->id,
            "cancellation_rate" => $this->job_cancellation_rate,
            "feedback_avg" => $this->overall_feedback_rating,
            "cet" => $this->user_extra_info->cet ?? "0",
            "cetriz" => null
        ];
    }
}