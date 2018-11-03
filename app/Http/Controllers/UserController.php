<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

use App\User;
use App\Cred;



class UserController extends Controller

{

    public function __construct()

    {

        //  $this->middleware('auth:api');
        $this->middleware('BasicAuth');

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function authenticate(Request $request)

    {

        $this->validate($request, [

            'email' => 'required',

            'password' => 'required'

        ]);

        $user = User::where('email', $request->input('email'))->first();

        if(Hash::check($request->input('password'), $user->password)){

            $apikey = base64_encode(str_random(40));

            User::where('email', $request->input('email'))->update(['api_key' => "$apikey"]);;

            return response()->json(['status' => 'success','api_key' => $apikey]);

        }else{

            return response()->json(['status' => 'fail'],401);

        }

    }

    public function show($id)
    {
        return view('user_edit', ['userData' => User::where('idGatewayUser', $id)->get()->first()->toArray()]);

    }

    public function add()
    {
        return view('user_add');

    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'input-username' => 'required',
            'input-password' => 'required',
        ]);

        $newCred = new User();
        $data = $request->all();
        $data["username"] = $data["input-username"];
        $data['password'] = Hash::make($data['input-password']);

        $newCred->fill($data);
        $newCred->save();
        return view('admin_show', ['credData' => Cred::all(), 'userData' => User::all()]);
    }

    public function edit($id)
    {
        return view('user_edit', ['userData' => User::where('idGatewayUser', $id)->get()->first()->toArray()]);

    }

    public function store(Request $request, $id)
    {
        $this->validate($request, [
            'idGatewayUser' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);

        $params = $request->all();
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'fail']);
        } else {
            $user->update($params);
        }

        return view('admin_show', ['credData' => Cred::all(), 'userData' => User::all()]);
    }

    public function delete($id)
    {
        User::where('idGatewayUser', $id)->delete();
    }

}

?>