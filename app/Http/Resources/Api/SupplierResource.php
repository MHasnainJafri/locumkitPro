<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            "supplier_id" => $this->id,
            "name" => $this->name,
            "store_name" => $this->store_name,
            "address" => $this->address,
            "addresssec" => $this->second_address,
            "town" => $this->town,
            "country" => $this->country,
            "postcode" => $this->postcode,
            "email" => $this->email,
            "contact_no" => $this->contact_no,
            "automaticinvoice" => $this->automatic_invoice,
            "status" => $this->status,
            "created_by" => $this->created_by_user_id,
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}