<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Token;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivation;


class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        
    }

    public function register(Request $request) {

        $this->validate($request, [
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);

        $user=User::where('email', $request->input('email'))->first();

        if($user) {

            return response()->json(['error'=>'User already exists', 'url'=>'api/signup'], 403);
        }

        $hasher=app()->make('hash');

        // GENERATING THE ACTIVATION TOKEN AND STORING IN THE DB

        $token=new Token();

        $user=User::create([
                'name'=>$request->input('name'),
                'email'=>$request->input('email'),
                'password'=>$hasher->make($request->input('password')),
                'activation_token'=>$token->getHash()
            ]);

        Mail::to($user)->send(new AccountActivation($user,$token->getToken()));

        return response()->json(['message'=>'Registration successful'], 200);
    }   


    public function activateAccount($token) {

        $token_obj=new Token($token);

        $user=User::where('activation_token', $token_obj->getHash())->first();

        if(!$user) {

            return response()->json(['error'=>'Invalid activation token','url'=>'api/account/activate'], 400);
        }

        $user->activation_token=null;

        $user->save();

        return response()->json(['message'=>'Account Activated successfully'], 200);
    }
}
