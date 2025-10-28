<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class FinanceTransactionResource extends JsonResource
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
            'trans_id' => $this->getTransactionNumber(),
            'trans_type' => $this->getTransactionType(),
            'supplier' => $this['supplier'],
            'store' => $this['store'],
            'status' => $this['status'],
            'location' => $this['location'],
            'job_type' => $this['job_type'],
            'job_date' => $this['job_date'],
            'job_rate' => set_amount_format($this['job_rate']),
            'job_id' => isset($this['job_id']) ? $this['job_id'] : 'N/A',
            'category' => $this->getCategoryType(),
            'id' => $this['id'],
            'fre_id' => $this['freelancer_id'],
            'emp_id' => $this['employer_id'] ?? "0",
            'bank_date' => $this['is_bank_transaction_completed'] ? get_date_with_default_format($this->bank_transaction_date) : null,
            'bank' => $this['is_bank_transaction_completed'] == 0 ? false : true,
            'description' => isset($this['description']) ? substr($this['description'], 0, 10) . '...' : '',
            'long_description' => isset($this['description']) ? $this['description'] : '',
            "income_type" => $this->getCategoryType(),
            "invoice_id" => $this->invoice_id ?? "",
            "invoice_notrequired" => $this->is_invoice_required ? "0" : "1",
            "receipt" =>  $this->receipt ? url($this->receipt) : "",
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}