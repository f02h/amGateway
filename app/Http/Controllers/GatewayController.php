<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Msg;
use App\Cred;
use App\User;
use Auth;
use Illuminate\Support\Facades\Crypt;

class GatewayController extends Controller
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
        return view('reg_show', ['credData' => Cred::all()]);
    }


    public function show($id)
    {
        return view('gateway_edit', ['gatewayData' => Cred::where('idGatewayCred', $id)->get()->first()->toArray()]);

    }

    public function add()
    {
        return view('gateway_add');

    }

    public function create(Request $request)
    {
        $this->validate($request, [
            "idGateway" => "required",
            'username' => 'required',
            'password' => 'required',
            'host' => 'required',
            'port' => 'required',
            'transport' => 'required'
        ]);

        $newCred = new Cred();
        $data = $request->all();
        $data['password'] = Crypt::encrypt($data["password"]);

        $newCred->fill($data);
        $newCred->save();
        return redirect("/admin");
    }

    public function edit($id)
    {
        $gatewayData = Cred::where('idGatewayCred', $id)->get()->first()->toArray();
        $gatewayData["password"] = Crypt::decrypt($gatewayData["password"]);
        return view('gateway_edit', ['gatewayData' => $gatewayData]);

    }

    public function store(Request $request, $id)
    {
        $this->validate($request, [
            "idGateway" => "required",
            'username' => 'required',
            'password' => 'required',
            'host' => 'required',
            'port' => 'required',
            'transport' => 'required'
        ]);

        $params = $request->all();
        $user = Cred::find($id);
        if (!$user) {
            return response()->json(['status' => 'fail']);
        } else {
            $params["password"] = Crypt::encrypt($params["password"]);
            $user->update($params);
        }
        return redirect("/admin");
    }

    public function delete($id)
    {
        Cred::where('idGatewayCred', $id)->delete();
    }

}