<?php

namespace App\Services;

use App\Models\ContactUs;

class ContactUsService
{
    public function GetContactUs()
    {
        return ContactUs::where('id', 1)->first();
    }

    public function SetContactUs($request)
    {
        $contact = ContactUs::where('id', 1)->first();

        $contact->email = $request->email;
        $contact->website = $request->website;
        $contact->phone_number = $request->phone_number;

        $contact->save();
    }
}
