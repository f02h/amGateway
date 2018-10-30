<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;
use App\Msg;
use App\Cred;
use Auth;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        return view('reg_show', ['credData' => Cred::all(), 'tokenData' => Token::all()]);
    }


}