<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceEmployerResource extends JsonResource
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
            "job_id" => $this->job_id,
            "emp_id" => $this->employer_id,
            "fre_id" => $this->freelancer_id,
            "fre_type" => $this->freelancer_type,
            "job_date" => get_date_with_default_format_app($this->job_date),
            "job_rate" => set_amount_format($this->job_rate),
            "bonus" => set_amount_format($this->bonus ?? 0),
            "paid" => $this->is_paid ? 1 : null,
            "paid_date" => $this->paid_date,
            "status" => $this->status,
            "created_at" => $this->created_at->toDateTimeString(),
            "total" => set_amount_format($this->job_rate + ($this->bonus ?? 0)),
            'locum_type' => $this->freelancer_type,
            'in_jobno' => $this->job_id,
            'in_locum' => $this->freelancer_id,
            'in_date' => get_date_with_default_format_app($this->job_date),
            'in_rate' => $this->job_rate,
            'in_bonus' => $this->bonus,
            'in_paid' => $this->is_paid ? "1" : null,
            'in_paiddate' => $this->is_paid ? get_date_with_default_format_app($this->paid_date) : null
        ];
    }
}