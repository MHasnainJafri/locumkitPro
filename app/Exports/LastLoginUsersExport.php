<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LastLoginUsersExport implements FromCollection, WithHeadings
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
        // return collect($this->data);
        return collect($this->data->map(function ($user) {
            return [
                'User_id'     => $user->id,
                'First_Name'  => $user->firstname,
                'Last_Name'   => $user->lastname,
                'Email'       => $user->email,
                'Profession'  => $user->user_acl_profession->name,
                'User Type'   => $user->role->name,
                'Last Login Date'  => optional($user->GetLastloginUsers)->login_time,
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
            'User Id',
            'First Name',
            'Last Name',
            'Email',
            'Profession',
            'User Type',
            'Last Login Date',
        ];
    }
}
