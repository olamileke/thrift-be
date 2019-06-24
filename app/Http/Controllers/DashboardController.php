<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    
    public function fetchCurrentDetails(Request $request) {

        $user=$request->auth;

        return response()->json(['data'=>new UserResource($user)], 200);
    }
}
