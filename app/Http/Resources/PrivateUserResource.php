<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateUserResource extends JsonResource
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
            "p_uid" => $this->id,
            "emp_id" => $this->employer_id,
            "p_name" => $this->name,
            "p_email" => $this->email,
            "p_mobile" => $this->mobile,
            "status" => $this->status,
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}