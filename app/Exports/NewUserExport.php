<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewUserExport implements FromCollection, WithHeadings
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
    // public function collection()
    // {
    //     return collect($this->data);
    // }
    
    public function collection()
    {
        return collect($this->data->map(function ($user) {
            return [
                'User_id'     => $user->id,
                'First_Name'  => $user->firstname,
                'Last_Name'   => $user->lastname,
                'Email'       => $user->email,
                'Profession'  => $user->user_acl_profession->name,
                'User Type'   => $user->role->name,
                'Sign Up Dates' => $user->created_at->format('Y-m-d'),
            ];
        }));
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings() :array
    {
        return [
            'User_id',
            'First_Name',
            'Last_Name',
            'Email',
            'Profession',
            'User Type',
            'Sign Up Dates',
        ];
    }
}