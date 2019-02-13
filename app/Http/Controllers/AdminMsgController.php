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
        return view('live_search');
    }

    function action(Request $request)
    {
        if($request->ajax())
        {
            $output = '';
            $query = $request->get('query');
            if($query != '')
            {
                $data = DB::table('tbl_customer')
                    ->where('CustomerName', 'like', '%'.$query.'%')
                    ->orWhere('Address', 'like', '%'.$query.'%')
                    ->orWhere('City', 'like', '%'.$query.'%')
                    ->orWhere('PostalCode', 'like', '%'.$query.'%')
                    ->orWhere('Country', 'like', '%'.$query.'%')
                    ->orderBy('CustomerID', 'desc')
                    ->get();
                $data = Msg::where('domain', 'like', '%'.$query.'%')->get();
            }
            else
            {
                $data = DB::table('tbl_customer')
                    ->orderBy('CustomerID', 'desc')
                    ->get();
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