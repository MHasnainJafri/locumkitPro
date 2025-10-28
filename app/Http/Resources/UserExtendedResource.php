<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExtendedResource extends JsonResource
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
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "mobile" => $this->user_extra_info->mobile,
            "address" => $this->user_extra_info->address,
            "city" => $this->user_extra_info->city,
            "company" => $this->user_extra_info->company,
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}