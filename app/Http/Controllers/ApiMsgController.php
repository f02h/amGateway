<?php

namespace App\Http\Controllers;

use App\Register\EPP;
use Illuminate\Http\Request;
use App\Msg;
use Auth;
use App\Cred;
use Illuminate\Support\Facades\Crypt;



class ApiMsgController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
//        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $todo = Auth::user()->todo()->get();
        return response()->json(['status' => 'success','result' => $todo]);
    }

    public function getMessages($action, Request $request)
    {
        $idGateway = $request->input('gateway');
        if (!$idGateway) {
            return response()->json(['status' => 'error', 'msg' => 'No gateway parameter set.']);
        }

        switch ($action) {
            case 'transfer_in':
                $action = EPP::DOMAIN_TRANSFER_IN;
                break;
            case 'transfer_out':
                $action = EPP::DOMAIN_TRANSFER_OUT;
                break;
            default:
                break;
        }

        $result = array();
        foreach (Msg::select()->where('idGateway', $idGateway)->where('msgAction', $action)->get() as $msg) {
            $result[] = $msg->domain;
        }

        return response()->json(['status' => 'success', 'domains' => $result]);

    }

}