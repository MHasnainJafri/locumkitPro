<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobPostExtendedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $timeline_job_data = array();
        foreach ($this->job_post_timelines as $job_post_timeline) {
            $timeline_job_data[] = [
                "tid" => $job_post_timeline->id,
                "job_id" => $job_post_timeline->job_post_id,
                "job_date_new" => get_date_with_default_format($job_post_timeline->job_date_new),
                "job_timeline_hrs" => $job_post_timeline->job_timeline_hrs,
                "job_rate_new" => $job_post_timeline->job_rate_new,
                "job_timeline_status" => $job_post_timeline->job_timeline_status,
            ];
        }
        return [
            "job_id" => $this->id,
            "e_id" => $this->employer_id,
            "cat_id" => $this->user_acl_profession_id,
            "job_title" => $this->job_title,
            "job_date" => get_date_with_default_format($this->job_date),
            "job_start_time" => $this->job_start_time,
            "job_post_timeline_hrs" => "",
            "job_post_desc" => $this->job_post_desc,
            "job_rate" => $this->job_rate,
            "job_type" => $this->job_type,
            "job_address" => $this->job_address,
            "job_region" => $this->job_region,
            "job_zip" => $this->job_zip,
            "store_id" => $this->employer_store_list_id,
            "job_status" => $this->job_status,
            "job_relist" => $this->job_relist,
            "job_create_date" => $this->created_at->toDateTimeString(),
            "job_update_date" => $this->updated_at->toDateTimeString(),
            "timeline_job_data" => $timeline_job_data
        ];
    }
}