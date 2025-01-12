<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticController extends Controller
{

    public function about()
    {
        return view('pages.statics.about');
    }

    public function contact()
    {
        return view('pages.statics.contact-us');
    }

    public function storeContact(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]); 

        return redirect()->back()->with('success', 'Your message has been sent successfully.');
    }

    public function faq()
    {
        return view('pages.statics.faq');
    }
}