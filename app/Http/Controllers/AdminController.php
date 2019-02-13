<?php

namespace App\Http\Controllers;

use App\Register\EPP;
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
        return view('admin_show', ['gatewayStats' => $this->getGatewaysStat(),'credData' => Cred::all(), 'userData' => User::all(), 'supportedStatus' => EPP::$supportedMessages]);
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
            $lastMsgRow = Msg::where('idGateway', $cred['idGateway'])->orderBy('idGatewayMsg', 'desc')->first();
            if ($lastMsgRow) {
                $gateways[$cred['idGateway']] = $lastMsgRow->msgDate;
            }
        }
        return $gateways;
    }

}