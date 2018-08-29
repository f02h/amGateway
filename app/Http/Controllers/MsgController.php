<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Msg;
use Auth;
use App\Cred;
use Illuminate\Support\Facades\Crypt;



class MsgController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'todo' => 'required',
            'description' => 'required',
            'category' => 'required'
        ]);
        if(Auth::user()->todo()->Create($request->all())){
            return response()->json(['status' => 'success']);
        }else{
            return response()->json(['status' => 'fail']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        foreach (Cred::all() as $gateway) {
            $epp = null;
            if ($gateway->idGateway == 'Arnes') {
                $epp = new \App\Register\Arnes($gateway->username, Crypt::decrypt($gateway->password), $gateway->transport, $gateway->host, $gateway->port);
            }

            if ($epp) {
                $epp->readMessages();
            }
        }
        return response()->json(['status' => 'success']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Msg::destroy($id)){
            return response()->json(['status' => 'success']);
        }
    }
}