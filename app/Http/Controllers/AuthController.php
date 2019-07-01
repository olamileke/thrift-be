<?php

namespace App\Http\Controllers;
use App\User;
use App\Token;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

use App\Mail\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
            return response()->json(['error'=>'Username or Password is wrong','url'=>'api/login'], 400);
        }

        $hasher=app()->make('hash');

        if($hasher->check($request->input('password'), $user->password)) {
            
            return response()->json(['token'=>$this->jwt($user),'user'=>$user],200);
        }

        return response()->json(['error'=>'Username or Password is wrong','url'=>'api/login'], 400);
    }


    public function sendResetPasswordMail(Request $request) {

        $this->validate($request, [

            'email'=>'required'
        ]);


        $user=User::where('email', $request->input('email'))->first();

        if(!$user) {

            return response()->json(['error'=>'User does not exist', 'url'=>'api/sendpasswordresetmail'], 400);
        }

        $token=new Token();

        $now=strtotime(date('Y-m-d h:i:s'));
        $expiry=$now + (60 * 30);

         DB::insert('insert into password_resets(user_id, reset_token, expiry) values(?,?,?)', [$user->id, $token->getHash(), Date('Y-m-d h:i:s', $expiry)]);

        Mail::to($user)->send(new ResetPassword($user, $token->getToken()));
    }

}
