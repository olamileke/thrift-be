<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard=null)
    {
        $token=$request->input('api_token');

        if(!$token) {

            return response()->json(['error'=>'Token not provided'], 401);
        }

        try {

            $credentials=JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        }
        catch(ExpiredException $e) {

            return response()->json(['error'=>'Expired token'], 400);
        } 
        catch(Exception $e) {

            return response()->json(['error'=>'An error while decoding token'], 400);
        }  


        $user=User::find($credentials->sub);

        $request->auth=$user;

        return $next($request);
    }
}
