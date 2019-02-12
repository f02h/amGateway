<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;
use App\Msg;
use App\Cred;
use App\User;
use Auth;
use Illuminate\Support\Facades\Crypt;

use App\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

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
        return view('admin_show', ['gatewayStats' => $this->getGatewaysStat(),'credData' => Cred::all(), 'userData' => User::all(), 'msgData' => Msg::orderBy('idGatewayMsg', 'desc')->take(5)->get()]);
    }

    public function logout() {
        return redirect('/');
    }

    public function sendmail() {
        Mail::to('devel@klaro.si')->send(new Mailer('Arnes', 'test'));
    }

    public function getGatewaysStat() {
        $gateways = array();
        foreach (Cred::all() as $cred) {
            $gateways[$cred->idGatewayCred] = Msg::orderBy('idGatewayMsg', 'desc')->take(1)->get('date');
        }
    }

}