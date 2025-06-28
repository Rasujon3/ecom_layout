<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Courier;
use DataTables;

class OrderController extends Controller
{   

	public function __construct()
    {
        $this->middleware('auth_check');
    }

    public function orders(Request $request) 
    {
    	try
        {
            if($request->ajax()){

               $orders = Orderdetail::leftJoin('couriers','orderdetails.id','couriers.orderdetail_id')->select('orderdetails.*','couriers.invoice_no','couriers.consignment_id','couriers.tracking_code')->where('orderdetails.domain_id',getDomain()->id)->latest();

                    return Datatables::of($orders)
                        ->addIndexColumn()

                        ->addColumn('order_id', function($row){
                            $courier = courier($row->id);
                            $result = $courier?$courier->consignment_id:"Order-00".$row->id;
                            return $result;
                        })

                        ->addColumn('order_date', function($row) {
                            return $row->created_at->format('d M Y h:i a');
                        })

                       ->addColumn('status', function($row) {
                            $statuses = ['Pending', 'Accept', 'Cancel'];

                            // Find the current status index
                            $currentIndex = array_search($row->status, $statuses);

                            $html = "<select class='form-control change-status' data-id='".$row->id."'>";

                            foreach ($statuses as $index => $status) {
                                // Disable previous statuses (index < currentIndex)
                                $disabled = ($index < $currentIndex && $row->status != 'Cancel') ? "disabled" : "";

                                // Mark current status as selected
                                $selected = ($row->status == $status) ? "selected" : "";

                                $html .= "<option value='{$status}' {$selected} {$disabled}>{$status}</option>";
                            }

                            $html .= "</select>";

                            return $html;
                        }) 


                       ->addColumn('courier_status', function($row) {
                            $courier = courier($row->id);
                            if($courier)
                            {
                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                  CURLOPT_URL => "https://portal.packzy.com/api/v1/status_by_cid/{$courier->consignment_id}",
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_ENCODING => '',
                                  CURLOPT_MAXREDIRS => 10,
                                  CURLOPT_TIMEOUT => 0,
                                  CURLOPT_FOLLOWLOCATION => true,
                                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                  CURLOPT_CUSTOMREQUEST => 'GET',
                                  CURLOPT_HTTPHEADER => array(
                                    'Accept: application/json',
                                    'Content-Type: application/json',
                                    'Api-Key: 4n8bjp8rtwqkof6aykmzixtyit2ibske',
                                    'Secret-Key: j6sedrotktdkvbueiacuahyd'
                                  ),
                                ));

                                $response = curl_exec($curl);

                                $result = json_decode($response,true);

                                return $result['delivery_status'];
                            }else{
                                return "-";
                            }
                            

                        })
                       
                        ->addColumn('action', function($row){
                                                        
                           $btn = "";
                           $btn .= '&nbsp;';
                           $btn .= ' <button type="button" class="btn btn-success btn-sm action-button edit-order customer-discount" data-id="'.$row->id.'"><i class="fa fa-percent"></i></button>';

                           $btn .= '&nbsp;';
        
                            

                            $btn .= ' <a href="'.url('/show-invoice/'.$row->id).'" class="btn btn-primary btn-sm action-button edit-order" data-id="'.$row->id.'"><i class="fa fa-eye"></i></a>';

                            $btn .= '&nbsp;';


                            $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-order action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>'; 

                             
        
                            return $btn;
                        })->filter(function ($instance) use ($request) {

                            if ($request->get('search') != "") {
                                $instance->where(function($w) use($request){
                                    $search = $request->get('search');
                                    $w->orWhere('orderdetails.customer_name', 'LIKE', "%$search%")->orWhere('orderdetails.customer_phone', 'LIKE', "%$search%")->orWhere('couriers.consignment_id','LIKE',"%$search%");
                                });
                            } 

                            if ($request->get('from_date') != "") {
                                 $instance->where(function($w) use($request){
                                    $w->orWhereDate('orderdetails.created_at', '>=', $request->from_date);
                                });
                            } 

                            if ($request->get('to_date') != "") {
                                 $instance->where(function($w) use($request){
                                    $w->orWhereDate('orderdetails.created_at', '<=', $request->to_date);
                                });
                            }

                            if ($request->get('status') != "") {
                                 $instance->where(function($w) use($request){
                                    $status = $request->get('status');
                                    $w->orWhere('orderdetails.status', $status);
                                });
                            }

                                
                        })->setRowID('id')
                        ->rawColumns(['action','status','order_id','order_date','courier_status'])
                        ->make(true);
            }
            return view('orders.my_order');
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function deleteOrder($id)
    {
        try
        {
            $order = Orderdetail::findorfail($id);
            $order->orders()->delete();
            if($order->courier)
            {
                $order->courier->delete();
            }
            $order->delete();
            return response()->json(['status'=>true, 'message'=>'Successfully the order has been deleted']);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function showInvoice($id)
    {
        try
        {
            $order = Orderdetail::with('orders')->findorfail($id);
            //return $order;
            return view('orders.invoice',compact('order'));
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function printInvoice($id)
    {
        try
        {   
            $order = Orderdetail::with('orders')->findorfail($id);
            return view('orders.print_invoice',compact('order'));
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function searchCourierOrder()
    {
        return view('orders.search');
    }
}
