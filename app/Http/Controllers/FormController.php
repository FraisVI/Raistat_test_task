<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        return view('form.index');
    }

    public function store()
    {
        $data = \request()->validate([
            'name' => 'string',
            'email' => 'email:rfc,dns',
            'phone_number' => 'numeric',
            'sum' => 'numeric',
            'more_30_seconds' => 'boolean',
        ]);
//        return redirect()->route('form.index');
    }
}
