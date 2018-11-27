<?php

namespace App\Http\Controllers;

use App\Register\EPP;
use App\User;
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

    public function getMessages( Request $request)
    {
        $idGateway = $request->input('gateway');
        $user = $request->auth->username;
        if (!$idGateway) {
            return response()->json(['status' => 'error', 'msg' => 'No gateway parameter set.']);
        }

        $result = array();
        foreach (Msg::select()->where('idGateway','like', '%'.$idGateway.'%')->whereNull('status')->get() as $msg) {
            try {
                $msg->status = 'PROCESSING';
                $msg->instance = $user;
                $msg->save();
            } catch (\Exception $exception) {
                return response()->json(['status' => 'error', 'msgs' => $exception->getMessage()]);
            }
            $result[] = json_decode($msg->msg);
        }

        return response()->json(['status' => 'success', 'msgs' => $result]);

    }

    public function releaseMessages(Request $request) {
        $idGateway = $request->input('gateway');
        if (!$idGateway) {
            return response()->json(['status' => 'error', 'msg' => 'No gateway parameter set.']);
        }

        $user = User::where('username', $request->auth->username)->first();

        if ($user->instance == 'SI') {
            $instance = 'siInstance';
        } else {
            $instance = 'hrInstance';
        }

        $result = array();
        foreach (Msg::select()->where('idGateway','like', '%'.$idGateway.'%')->where('status', 'PROCESSING')->get() as $msg) {
            try {
                $msg->status = null;
                $msg->instance = null;
                $msg->{$instance} = 'NOT_INTERESTED';
                $msg->save();
            } catch (\Exception $exception) {
                return response()->json(['status' => 'error', 'msgs' => $exception->getMessage()]);
            }
            $result[] = json_decode($msg->msg);
        }

        return response()->json(['status' => 'success', 'msgs' => $result]);
    }

    public function confirmMessages(Request $request) {
        $idMessages = $request->input('idMessages');

        if (!$idMessages) {
            return response()->json(['status' => 'error', 'msg' => 'No id to confirm.']);
        }

        $user = User::where('username', $request->auth->username)->first();

        if ($user->instance == 'SI') {
            $instance = 'siInstance';
        } else {
            $instance = 'hrInstance';
        }

        try {
            if (is_array($idMessages)) {
                foreach ($idMessages as $id) {
                    Msg::where('idGatewayMsg', $id)->update(array('status' => 'ACC'));
                    Msg::where('idGatewayMsg', $id)->update(array($instance => 'ACC'));
                }
            } else {
                Msg::where('idGatewayMsg', $idMessages)->update(['status' => 'ACC']);
                Msg::where('idGatewayMsg', $idMessages)->update([$instance => 'ACC']);
            }
        } catch (\Exception $exception) {
            return response()->json(['status' => 'error', 'msg' => $exception->getMessage()]);
        }

        return response()->json(['status' => 'success']);
    }

}