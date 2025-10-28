<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocumPrivateJobReport implements FromCollection, WithHeadings
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
            if ($user->PrivateUser->count() > 0) {
                foreach ($user->PrivateUser as $privateUser) {
                    if (in_array($privateUser->id, $this->arr_ids)) {
                        $data->push([
                            'Name' => $privateUser->name ?? '',
                            'Email' => $privateUser->email ?? '',
                            'Employer Name' => $user->firstname . ' ' . $user->lastname,
                            'Employer ID' => $user->id ?? '',
                            'Profession' => $user->user_acl_profession->name ?? '',
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
            'Name',
            'Email',
            'Employer Name',
            'Employer Id',
            'Profession',
        ];
    }
}
