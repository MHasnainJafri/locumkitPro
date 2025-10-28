<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\UserPaymentInfo;
use Illuminate\Http\Request;

class paymentController extends Controller
{


    // ###  table  =  user_payment_infos
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UserPaymentInfo::with('user');

    if ($search = $request->input('search')) {
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('firstname', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })
        ->orWhere('payment_type', 'like', "%{$search}%")
        ->orWhere('payment_token', 'like', "%{$search}%")
        ->orWhere('price', 'like', "%{$search}%");
    }

    $paymentHistory = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.payment.index', compact('paymentHistory'));
        //
        // $paymentHistory = UserPaymentInfo::with('user')->paginate(10);
        // return view('admin.payment.index', compact('paymentHistory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function paymentDelete($id)
    {

        $payment=UserPaymentInfo::find($id);
        $payment->delete();
return redirect()->back()->with('success','deleted successfully');        //
    }


}
