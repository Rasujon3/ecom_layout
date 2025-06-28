<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Http\Requests\SettingRequest;
use Auth;
use Hash;
class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }

    public function appSettings()
    {   
    	$setting = setting();
    	return view('settings.app_settings',compact('setting'));
    }

    public function settingApp(SettingRequest $request)
    {
        try
        {   
            // Fetch current user's setting (not always ID 1)
            $data = Setting::where('user_id', user()->id)->first();

            $defaults = [
                'courier_api_key'       => $data ? $data->courier_api_key : null,
                'courier_secret'        => $data ? $data->courier_secret : null,
                'order_note'            => $data ? $data->order_note : null,
                'facebook_pixel_id'     => $data ? $data->facebook_pixel_id : null,
                'pathao_client_id'      => $data ? $data->pathao_client_id : null,
                'pathao_client_secret'  => $data ? $data->pathao_client_secret : null,
                'pathao_access_token'   => $data ? $data->pathao_access_token : null,
                'delivery_charge'       => $data ? $data->delivery_charge : null
            ];

            Setting::updateOrCreate(
                ['user_id' => user()->id],
                [
                    'user_id'              => user()->id,
                    'courier_api_key'      => $request->courier_api_key ?? $defaults['courier_api_key'],
                    'courier_secret'       => $request->courier_secret ?? $defaults['courier_secret'],
                    'order_note'           => $request->order_note ?? $defaults['order_note'],
                    'facebook_pixel_id'    => $request->facebook_pixel_id ?? $defaults['facebook_pixel_id'],
                    'pathao_client_id'     => $request->pathao_client_id ?? $defaults['pathao_client_id'],
                    'pathao_client_secret' => $request->pathao_client_secret ?? $defaults['pathao_client_secret'],
                    'pathao_access_token'  => $request->pathao_access_token ?? $defaults['pathao_access_token'],
                    'delivery_charge'      => $request->delivery_charge ?? $defaults['delivery_charge'],
                ]
            );

            $notification = [
                'messege'    => 'Successfully updated',
                'alert-type' => 'success',
            ];

            return redirect()->back()->with($notification); 

        } catch (Exception $e) {
            return response()->json([
                'status'  => false, 
                'code'    => $e->getCode(), 
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function passwordChange()
    {
        return view('settings.change_password');
    }

    public function changePassword(Request $request)
    {
        try
        {
            $user = User::findorfail(Auth::user()->id);

            

            if (!Hash::check($request->current_password, $user->password)) {
    

                $notification=array(
                             'messege'=>'The current password is not matched',
                             'alert-type'=>'error'
                            );

                return redirect()->back()->with($notification);
            }

            $user->password = Hash::make($request->new_password);
            $user->update();


           $notification=array(
                             'messege'=>'Successfully your has been changed',
                             'alert-type'=>'success'
                            );

            return redirect()->back()->with($notification);
            
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function metaPixelSettings()
    {
        $setting = setting();
        return view('settings.meta_pixel',compact('setting'));
    }

    public function setDelveryCharge()
    {
        $setting = setting();
        return view('settings.delivery_charge', compact('setting'));
    }
}
