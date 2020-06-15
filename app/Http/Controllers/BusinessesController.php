<?php

namespace App\Http\Controllers;

use App\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessesController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
        $this->middleware("roles:admin")->except('update');
    }

    // store
    public function store()
    {
        $business = Business::create($this->validateRequest());

        return redirect('/business');
    }

    // update 
    public function update(Business $business)
    {
        // does the user the belongs to the business
        if ($business->users->contains(Auth::user()->id) || Auth::user()->role == 'admin') {
            $business->update($this->validateRequest());
        }
        return redirect($business->path());
    }

    // delete
    public function destroy(Business $business)
    {
        $business->delete();

        return redirect('/business');
    }

    // show
    public function show(Business $business)
    {
    }

    // validate request
    protected function validateRequest()
    {
        return request()->validate([
            'id' => '',
            'name' => 'required',
            'address' => '',
            'abn' => '',
            'phone' => '',
            'email' => '',
            'web' => '',
            'primary_contact_id' => '',
            'notes' => '',
        ]);
    }
}
