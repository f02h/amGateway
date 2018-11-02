<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Msg;
use App\Token;
use Auth;
use Illuminate\Support\Facades\Crypt;

class TokenController extends Controller
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
        return view('token_show', ['data' => Token::all()]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
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
            $params = $request->all();
            $cred = Cred::find($id);
            $cred->update($params);

            return view('reg_show', ['data' => Cred::all()]);
        }else{
            return response()->json(['status' => 'fail']);
        }

    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'input-idGateway' => 'required',
            'input-username' => 'required',
            'input-password' => 'required',
            'input-host' => 'required',
            'input-port' => 'required',
            'input-transport' => 'required'
        ]);
        if(Auth::user()){


            $newCred = new Cred();
            $data = $request->all();
            $data['password'] = Crypt::encrypt($data['input-password']);

            $newCred->fill($data);
            $newCred->save();
            return view('reg_show', ['data' => Cred::all()]);
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
        return view('reg_edit', ['data' => Cred::where('idGatewayCred', $id)->get()->first()->toArray()]);

    }

    public function edit($id)
    {
        return view('token_edit', ['data' => Cred::where('idGatewayCred', $id)->get()->first()->toArray()]);

    }

    public function delete($id)
    {
        Cred::where('idGatewayCred', $id)->delete();
    }
}