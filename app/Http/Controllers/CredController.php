<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Msg;
use App\Cred;
use Auth;
use Illuminate\Support\Facades\Crypt;

class CredController extends Controller
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
        return view('reg_show', ['data' => Cred::all()]);
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
            'idGateway' => 'required',
            'username' => 'required',
            'password' => 'required',
            'host' => 'required',
            'port' => 'required',
            'transport' => 'required'
        ]);
        if(Auth::user()){
            $newCred = new Cred();
            $data = $request->all();
            $data['password'] = Crypt::encrypt($data['password']);

            $newCred->fill($data);
            $newCred->save();
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
    public function show()
    {
        return view('reg_show', ['data' => Cred::all()]);

    }

    public function edit($id)
    {
        return view('reg_edit', ['data' => Cred::where('idGatewayCred', $id)->first()]);

    }
}