<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;
use App\Msg;
use App\Cred;
use App\User;
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
        $this->middleware('BasicAuth');
    }

    public function index() {
        return view('admin_show', ['credData' => Cred::all(), 'userData' => User::all(), 'msgData' => Msg::orderBy('idGatewayMsg', 'desc')->take(5)->get()]);
    }

    public function logout() {
        return redirect('/');
    }


}