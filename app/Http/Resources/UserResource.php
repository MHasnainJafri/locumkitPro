<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "id" => $this->id,
            "password" => $this->password,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "active" => $this->active,
            "user_acl_role_id" => $this->user_acl_role_id,
            "user_acl_profession_id" => $this->user_acl_profession_id,
            "user_acl_package_id" => $this->user_acl_package_id,
            "is_free" => $this->is_free,
            "created_at" => $this->created_at->format("Y-m-d"),
            "financial_year" => isset($this->financial_year) && $this->financial_year ? $this->financial_year->toArray() : [
                "id" => 0,
                "user_id" => strval($this->id),
                "user_type" => "soletrader",
                "month_start" => "4",
                "month_end" => "3",
                "created_at" => $this->created_at,
                "updated_at" => $this->created_at
            ],
            'mobile_app_token' => isset($this->mobile_app_token) ? $this->mobile_app_token : ''
        ];
    }
}
