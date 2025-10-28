<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployerTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'transaction_id' => $this->id,
            'job_id' => $this->job_id,
            'date' => $this->job_date,
            'locum_id' => $this->freelancer_id,
            'bonus' => $this->bonus,
            'total' => $this->bonus + $this->job_rate,
            'is_paid' => $this->is_paid,
            'paid_date'=> $this->paid_date,
            'locum_type' => $this->freelancer_type,
            ];
        // return parent::toArray($request);
    }
}
