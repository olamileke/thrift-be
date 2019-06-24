<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }


    // GENERATING THE JWT TOKEN AND RETURNING IT UPON LOGIN

    public function jwt(User $user) {

        $payload=[
            'iss'=>'Thrift',
            'sub'=>$user->id,
            'iat'=>time(),
            'exp'=>time() + 14 * 24 * 60 * 60
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function authenticate(Request $request) {

        $this->validate($request, [
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);

        $user=User::where('email', $request->input('email'))->first();

        if(!$user) {
            return response()->json(['error'=>'Username or Password is wrong'], 400);
        }

        $hasher=app()->make('hash');

        if($hasher->check($request->input('password'), $user->password)) {
            
            return response()->json(['token'=>$this->jwt($user)], 200);
        }

        return response()->json(['error'=>'Username or Password is wrong'], 400);
    }

}
