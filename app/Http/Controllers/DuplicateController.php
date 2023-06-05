<?php

namespace App\Http\Controllers;
use App\Models\Loan;
use App\Models\Client;
use Illuminate\Http\Request;

class DuplicateController extends Controller
{
    public function getDusp()
    {
        $limo = Loan::find(10)->get();

    
    }
}
