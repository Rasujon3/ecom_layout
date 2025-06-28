<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class IndexController extends Controller
{
    public function loginPage()
    {   
    	$routeName = Route::currentRouteName();
    	$title = $routeName == 'admin'?'Admin Panel':"User Panel";
    	return view('admin_login', compact('title','routeName'));
    }
}
