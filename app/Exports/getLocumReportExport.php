<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class getLocumReportExport implements FromCollection, WithHeadings
{
    protected $data;
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function __construct($data)
    {
        $this->data = $data;
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
        return collect($this->data);
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings() :array
    {
        return [
            'User Id',
            'Locum',
            'Jobs Applied',
            'Jobs Accepted',
            'jobs Complete',
            'Success Rate',
            'cancel Rate',
            'Jobs Frozen',
            'frozen_and_accepted',
            'frozen_success_rate',
            'private_jobs_added',
        ];
    }
}
