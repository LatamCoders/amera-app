<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class ExperienceController extends Controller
{
    /*
     * Calificar a amera
     */
    public function Rate(Request $request)
    {
        try {
            $ameraRate = new Experience();


        } catch (Exception $exception) {

        }
    }
}
