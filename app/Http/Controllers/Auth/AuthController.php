<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRiquest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @return View
     */
    public function showLogin()
    {
        return view('login/login_form');
    }

    /**
     * param App\Http\Requests\LoginFormRiquest
     $request
     */
    public function login(LoginFormRiquest $request)
    {
        
    }
}
