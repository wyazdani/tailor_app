<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Firebase\JWT\JWT;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        $key        =   env('JWT_KEY');
        $authorization  =   $request->header('Authorization');

        if(isset($authorization) && !empty($authorization)) {
            $access_token = explode('Bearer ', $authorization);
            if (count($access_token) ==2 && $access_token[0] =='')
            {
                try{
                    $token =    $access_token[1];
                    $decoded = JWT::decode($token, $key, array('HS256'));

                    if($decoded){
                        $user = User::where('access_token',$token)->first();
                        if ($user){
                            $request->merge(['user' => $user ]);
                            $request->setUserResolver(function () use ($user) {
                                return $user;
                            });
                            return $next($request);
                        }else{
                            return response()->json([
                                'status'    => false,
                                'messages'   => "Unauthenticated."
                            ],401);
                        }

                    }else{
                        return response()->json([
                            'status'    => false,
                            'messages'   => "Unauthenticated."
                        ],401);
                    }

                }
                catch(\Exception $ex){
                    return response()->json([
                        'status'    => false,
                        'messages'   => "Unauthenticated."
                    ],401);
                }

            }
            else{
                return response()->json([
                    'status'    => false,
                    'messages'   => "Unauthenticated."
                ],401);
            }
        }
        else{
            return response()->json([
                'status'    => false,
                'messages'   => "Unauthenticated."
            ],401);
        }
    }
}
