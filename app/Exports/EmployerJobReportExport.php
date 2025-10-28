<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployerJobReportExport implements FromCollection, WithHeadings
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
            'Employer',
            'Jobs Listed',
            'Jobs Accepted',
            'Success Rate',
            'Cancel Rate',
            'Number of Private job requests sent',
        ];
    }
}
