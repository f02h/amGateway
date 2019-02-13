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

class AdminMsgController extends Controller
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

    function index()
    {
        return view('search');
    }

    function action(Request $request)
    {
        if($request->ajax())
        {
            $output = '';
            $query = $request->get('query');
            $param = $request->get('param');
            $order = $request->get('order');
            if(!empty($query))
            {
                $data = Msg::query();
                foreach ($query as $qk => $qv) {
                    $data = $data->where($qk, 'like','%' . $qv . '%' );
                }

                $data = $data->get();

            }
            else
            {
                $data = Msg::where('domain', 'like', '%'.$query.'%')->get();
            }
            $total_row = $data->count();
            if($total_row > 0)
            {
                foreach($data as $row)
                {
                    $output .= '
                        <tr>
                         <td>'.$row->idGateway.'</td>
                         <td>'.$row->domain.'</td>
                         <td>'.$row->msgAction.'</td>
                         <td>'.$row->msgStatus.'</td>
                         <td>'.$row->msgDate.'</td>
                        </tr>
                        ';
                }
            }
            else
            {
                $output = '
       <tr>
        <td align="center" colspan="5">No Data Found</td>
       </tr>
       ';
            }
            $data = array(
                'table_data'  => $output,
                'total_data'  => $total_row
            );

            echo json_encode($data);
        }
    }

}