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

    public function getMessages( Request $request)
    {
        $idGateway = $request->input('gateway');
        if (!$idGateway) {
            return response()->json(['status' => 'error', 'msg' => 'No gateway parameter set.']);
        }

        $result = array();
        foreach (Msg::select()->where('idGateway','like', '%'.$idGateway.'%')->get() as $msg) {
            $result[] = json_decode($msg->msg);
        }

        return response()->json(['status' => 'success', 'msgs' => $result]);

    }

    public function confirmMessages(Request $request) {
        $idMessages = $request->input('idMessages');

        try {
            if (is_array($idMessages)) {
                foreach ($idMessages as $id) {
                    Msg::where('idGatewayMsg', $id)->update(array('status' => 'ACC'));
                }
            } else {
                Msg::where('idGatewayMsg', $idMessages)->update(array('status' => 'ACC'));
            }
        } catch (\Exception $exception) {
            return response()->json(['status' => 'failed', 'msg' => $exception->getMessage()]);
        }

        return response()->json(['status' => 'success']);
    }

}