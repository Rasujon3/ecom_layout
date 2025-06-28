<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\User;
use Validator;
use DB;
use Illuminate\Support\Facades\File;
use App\Models\Image;
use App\Models\Package;
use App\Models\Product;
use App\Models\Review;
use App\Models\Video;
use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Slider;
use SteadFast\SteadFastCourierLaravelPackage\Facades\SteadfastCourier;
use App\Models\Referlog;
use App\Models\Setting;

class ApiController extends Controller
{  

    public function saveDomain(Request $request)
    {   
    	DB::beginTransaction();
    	try
    	{
    		$validator = Validator::make($request->all(), [
    			'name' => 'required|string|max:50',
    			'email' => 'nullable|email|unique:users',
    			'phone' => 'required|string|min:11|unique:users',
	            'package_id' => 'required|integer|exists:packages,id',
	            'shop_name' => 'required|string|unique:domains',
	            'domain' => 'required|string|unique:domains',
	            'logo' => 'nullable',
	            'image' => 'nullable',
	            'address' => 'nullable',
	            //'max_product' => 'required|integer|min:1',
	        ]);

	        if ($validator->fails()) {
	        	DB::commit();
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        }

	        //$user = User::where('phone',$request->phone)->first();

	        $checkDomain = Domain::where('domain',$request->domain)->first();

	        if($checkDomain)
	        {   
	        	DB::commit();
	        	return response()->json(['status'=>false, 'domain_id'=>0, 'message'=>'The domain is not available'],400);
	        }

	        $shop_slug = str_replace(" ", "_", $request->shop_name);
 
	        if($request->file('image'))
	        {   
	            $file = $request->file('image');
	            $name = time().$shop_slug.$file->getClientOriginalName();
	            $file->move(public_path().'/uploads/users/', $name); 
	            $pathImg = 'uploads/users/'.$name;
	        }else{
	        	$pathImg = "defaults/profile.png";
	        }

	        if($request->file('logo'))
	        {   
	            $file = $request->file('logo');
	            $name = time().$shop_slug.$file->getClientOriginalName();
	            $file->move(public_path().'/uploads/shops/', $name); 
	            $pathLogo = 'uploads/shops/'.$name;
	        }else{
	        	$pathLogo = NULL;
	        }


	        $user = new User();
	        $user->name = $request->name;
	        $user->role_id = 2;
	        $user->email = $request->email;
	        $user->phone = $request->phone;
	        $user->password = bcrypt('123456');
	        $user->image = $pathImg;
	        $user->status = 'Inactive';
	        $user->refer_code = referCode();
	        $user->save();

	        $domain = new Domain();
	        $domain->user_id = $user->id;
	        $domain->theme_id = $request->package_id;
	        $domain->package_id = $request->package_id;
	        $domain->shop_name = $request->shop_name;
	        $domain->domain = $request->domain;
	        $domain->address = $request->address;
	        //$domain->max_product = $request->max_product;
	        $domain->logo = $pathLogo;
	        $domain->status = 'Inactive';
	        $domain->save();

	        DB::commit();
	        return response()->json(['status'=>true, 'domain_id'=>intval($domain->id), 'message'=>'Successfully a shop has been added']);

    	}catch(Exception $e){
    		DB::rollback();
    		return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    	}
    }

    public function domainLists(Request $request)
    {
    	try
    	{
    		$domains = Domain::latest()->where('status','Active')->get();
    		return response()->json(['status'=>count($domains)>0, 'data'=>$domains]);
    	}catch(Exception $e){
    		return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    	}
    }

    public function domainDetails(Request $request)
    {
    	try
    	{
    		$validator = Validator::make($request->all(), [
    			'domain' => 'required|string|exists:domains,domain',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        } 


	        $domain = Domain::with('theme')->where('domain',$request->domain)->first();

	        $infoData = Setting::where('user_id',$domain->user_id)->first();

	        return response()->json(['status'=>true, 'delivery_charge'=>$infoData?$infoData->delivery_charge:NULL, 'domain'=>$domain]);

    	}catch(Exception $e){
    		return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    	}
    }

    public function sliders(Request $request)
    {
    	try
    	{
    		$validator = Validator::make($request->all(), [
    			'domain' => 'required|string|exists:domains,domain',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        }

	        $domain = domainDetails($request);

	        $sliders = Slider::where('domain_id',$domain->id)->where('status','Active')->get();

	        return response()->json(['status'=>count($sliders)>0, 'total'=>count($sliders), 'data'=>$sliders]);

    	}catch(Exception $e){
    		return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    	}
    }

    public function products(Request $request)
    {
    	try
    	{
    		$validator = Validator::make($request->all(), [
    			'domain' => 'required|string|exists:domains,domain',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        }

	        $domain = domainDetails($request);

	        $products = Product::with('images')->where('domain_id',$domain->id)->where('status','Active')->get();

	        return response()->json(['status'=>count($products)>0, 'total'=>count($products), 'data'=>$products]);

    	}catch(Exception $e){
    		return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    	}
    }

    public function reviews(Request $request)
    {
    	try
    	{
    		$validator = Validator::make($request->all(), [
    			'domain' => 'required|string|exists:domains,domain',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        }

	        $domain = domainDetails($request);

	        $reviews = Review::where('domain_id',$domain->id)->where('status','Active')->latest()->get();

	        return response()->json(['status'=>count($reviews)>0, 'total'=>count($reviews), 'data'=>$reviews]);

    	}catch(Exception $e){
    		return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    	}
    }

    public function getVideo(Request $request)
    {
    	try
    	{
    		$validator = Validator::make($request->all(), [
    			'domain' => 'required|string|exists:domains,domain',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        }

	        $domain = domainDetails($request);

	        $video = Video::where('domain_id',$domain->id)->first();

	       // return $video;

	        return response()->json(['status'=>$video?true:false, 'data'=>$video?$video:new \stdClass()]);

    	}catch(Exception $e){
    		return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    	}
    }

    public function saveOrder(Request $request)
	{   
	    DB::beginTransaction();
	    try
	    {
	        $validator = Validator::make($request->all(), [
	            'domain' => 'required|string|exists:domains,domain',
	            'customer_name' => 'required|string',
	            'customer_phone' => 'required|string',
	            'customer_address' => 'required',
	            'district' => 'nullable',
	            'payment_method' => 'required|string',
	            'sub_total' => 'required|numeric',
	            'total' => 'required|numeric',
	            'orders' => 'required|array|min:1',
	            'refer_code' => 'nullable|string|exists:users,refer_code',
	            'delivery_charge' => 'nullable|numeric', 
	        ]);

	        if ($validator->fails()) {
	            DB::commit();
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        }

	        $domain = domainDetails($request);

	        $orderDetail = new Orderdetail();
	        $orderDetail->domain_id = $domain->id;
	        $orderDetail->customer_name = $request->customer_name;
	        $orderDetail->customer_phone = $request->customer_phone;
	        $orderDetail->customer_address = $request->customer_address;
	        $orderDetail->district = $request->district;
	        $orderDetail->payment_method = $request->payment_method;
	        $orderDetail->sub_total = $request->sub_total;
	        $orderDetail->delivery_charge = $request->delivery_charge;
	        $orderDetail->total = $request->total;
	        $orderDetail->save();

	        foreach ($request->orders as $item) {
	            $order = new Order();
	            $order->orderdetail_id = $orderDetail->id;
	            $order->product_id = $item['product_id'];
	            $order->product_price = $item['product_price'];
	            $order->qty = $item['qty'];
	            $order->unit_total = $item['unit_total'];
	            $order->save();
	        } 

	        if($request->has('refer_code'))
	        {
	        	$log = new Referlog();
	        	$log->user_id = $domain->user_id;
	        	$log->refer_code = $request->refer_code;
	        	$log->date = date('Y-m-d');
	        	$log->time = date('h:i A');
	        	$log->save();
	        }

	        DB::commit();
	        return response()->json([
	            'status' => true,
	            'order_id' => intval($orderDetail->id),
	            'message' => 'Successfully your order has been processed. We will contact you soon.'
	        ]);

	    } catch(Exception $e) {
	        DB::rollBack();
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}

	public function acceptCourierOrder(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(), [
	            'order_id' => 'required|string|exists:orderdetails,id',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);  
	        }

	        $order = Orderdetail::findorfail($request->order_id);
	        $orderData = [
			    'invoice' => '123456', 
			    'recipient_name' => 'John Doe',
			    'recipient_phone' => '01234567890',
			    'recipient_address' => 'Fla# A1,House# 17/1, Road# 3/A, Dhanmondi,Dhaka-1209',
			    'cod_amount' => 1000,
			    'note' => 'Handle with care'
			];

			$response = SteadfastCourier::placeOrder($orderData);

			return $response;

		}catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}

	public function addTempUser(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(), [
    			'name' => 'required|string|max:50',
    			'email' => 'nullable|email|unique:users',
    			'phone' => 'required|string|min:11|unique:users',
    			'package_id' => 'required|integer|exists:packages,id',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);   
	        }

	        $user = new User();
	        $user->role_id = 2;
	        $user->package_id = $request->package_id;
	        $user->name = $request->name;
	        $user->email = $request->email;
	        $user->password = bcrypt('123456');
	        $user->status = 'Active';
	        $user->save();
	        return response()->json(['status'=>true, 'user_id'=>intval($user->id), 'message'=>'Successfully an user has been added']);
		}catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}

	public function searchDomain(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(), [
	            'domain' => 'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);   
	        }

	        $search = $request->domain;
	        $data = Domain::where('domains.domain', 'LIKE', "%$search%")->latest()->get();
	        if(count($data) > 0)
	        {
	        	return response()->json(['status'=>false, 'message'=>'Sorry this domain is not available'],400);
	        }
	        return response()->json(['status'=>true, 'message'=>'The domain is available']);
		}catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}
	
	public function packages()
	{
	    try
	    {
	       $packages = Package::with(['services' => function ($query) {
                $query->where('status', 'Active');
            }])
            ->where('status', 'Active')
            ->whereHas('services', function ($query) {
                $query->where('status', 'Active');
            })
            ->latest()
            ->get();
	        return response()->json(['status'=>count($packages) > 0, 'data'=>$packages]);
	    }catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}
	
	public function privacyPolicy(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(), [
	            'domain' => 'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);   
	        }

	        $domain = Domain::where('domain',$request->domain)->first();
	        $user = User::findorfail($domain->user_id);
	        $data = Setting::select('privacy_policy')->where('user_id',$user->id)->first();
	        return response()->json(['status'=>true, 'data'=>$data]);
		}catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}
	
	public function contactUs(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(), [
	            'domain' => 'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);   
	        }

	        $domain = Domain::where('domain',$request->domain)->first();
	        $user = User::findorfail($domain->user_id);
	        $data = Setting::select('contact_name','contact_email','contact_phone','contact_address','contact_description')->where('user_id',$user->id)->first();
	        return response()->json(['status'=>true, 'data'=>$data]);
		}catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}
	
	public function aboutUs(Request $request)
	{
	    try
		{
			$validator = Validator::make($request->all(), [
	            'domain' => 'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);   
	        }

	        $domain = Domain::where('domain',$request->domain)->first();
	        $user = User::findorfail($domain->user_id);
	        $data = Setting::select('about_us')->where('user_id',$user->id)->first();
	        return response()->json(['status'=>true, 'data'=>$data]);
		}catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}


	public function adminInfo(Request $request)
	{
	    try
	    {
	        $validator = Validator::make($request->all(), [
	            'role_id' => 'required|integer',
	        ]);

	        if ($validator->fails()) {
	            return response()->json([
	                'status' => false, 
	                'message' => 'The given data was invalid', 
	                'data' => $validator->errors()
	            ], 422);   
	        }
	            
	        $user = User::where('role_id',$request->role_id)->first();
	        $data = Setting::select('privacy_policy','contact_name','contact_phone','contact_email','contact_description','contact_address','about_us')->where('user_id',$user->id)->first();
	        return response()->json(['status'=>true, 'data'=>$data]);
	    }catch(Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}

}
