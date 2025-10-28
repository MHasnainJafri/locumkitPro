<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocumPrivateJobsExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $arr_ids;
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function __construct($data,$arr_ids)
    {
        $this->data = $data;
        $this->arr_ids = $arr_ids;
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    $data = collect();

    foreach ($this->data as $user) {
        if ($user->Private_jobs && $user->Private_jobs->count() > 0) {
            foreach ($user->Private_jobs as $job) {
                if (in_array($job->id, $this->arr_ids)) {
                    $formattedDate = '';
                    if ($job->job_date) {
                        $formattedDate = \Carbon\Carbon::parse($job->job_date)->format('d/m/Y');  // Consistent format
                    }

                    $data->push([
                        'Locum Name' => $user->firstname . ' ' . $user->lastname,
                        'Locum ID' => $user->id ?? '',
                        'Employer Name' => $job->emp_name ?? '',
                        'Job Location' => $job->job_location ?? '',
                        'Job Rate' => $job->job_rate ?? '',
                        'Job Date' => $formattedDate,
                    ]);
                }
            }
        }
    }

    $data = $data->reverse();
    return $data;
}

    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings() :array
    {
        return [
            'Locum Name',
            'Locum Id',
            'Employer Name',
            'Location',
            'Rate',
            'Date',
        ];
    }
}
