<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\User;
use App\Utilities\UserHelper;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation_fields  =   [
            'email'        => 'required|string',
            'password'     => 'required|string',
        ];
        $validator     =  $this->getValidationFactory()->make($request->all(),$validation_fields);
        if($validator->fails()) {
            $messages   =   [];
            foreach ($validator->messages()->getMessages() as $key =>   $message){
                $messages[]    =
                    $message[0];
            }
            $messages =   implode(" ",$messages);
            return response()->json([
                'status'     =>  false,
                'messages'   =>  $messages
            ], 200);
        }
        $credentials =   [
            'email'         =>  $request->email,
            'password'      =>  $request->password,
        ];
        $user       =   User::where('email','=',$request->email)->first();
        auth()->attempt($credentials);
        $user   =   Auth::user();
        if (!$user){
            return response()->json([
                'status'     =>  false,
                'messages'   =>  'Incorrect credentials entered'
            ], 200);
        }
        $key        =   env('JWT_KEY');
        $token        =   JWT::encode(array('email'=>$user->email,
            'id'=>$user->id,
            'updated_at'=>$user->updated_at),$key);
        $user->access_token  =   $token;
        $user->save();
        $data_user  =   UserHelper::userObject($user->id);
        $data_user['status']  =   true;
        $data_user['messages']  =   'Login Successful';
        return response()->json($data_user, 200);
    }

    public function register(Request $request)
    {
        $validation_fields  =   [
            'email'         => 'required|email|unique:users',
            'name'    => 'required|max:255',
            'phone_number'  =>   'required|max:45|unique:users',
            'password'     => 'required|string|min:6|max:90',
            'type'     => 'required|in:customer,tailor',
        ];
        $validator     =  $this->getValidationFactory()->make($request->all(),$validation_fields);
        if($validator->fails()) {
            $messages   =   [];
            foreach ($validator->messages()->getMessages() as $key =>   $message){
                $messages[]    =
                    $message[0];
            }
            $messages =   implode(" ",$messages);
            return response()->json([
                'status'     =>  false,
                'messages'   =>  $messages
            ], 200);
        }
        $role   =   'customer';
        if ($request->type==='tailor'){
            $role   =   'tailor';
        }
        $user = User::create([
            'name'  =>  $request->name,
            'email'  =>  $request->email,
            'phone_number'  =>  $request->phone_number,
            'password'  =>  Hash::make($request->password),
            'role'  =>  $role,
            'address'   =>  '',
            'access_token'   =>  '',
        ]);
        $key        =   env('JWT_KEY');
        $token        =   JWT::encode(array('email'=>$user->email,
            'id'=>$user->id,
            'updated_at'=>$user->updated_at),$key);
        $user->access_token  =   $token;
        $user->save();
        $data_user =   UserHelper::userObject($user->id);
        $data_user['status']  =   true;
        $data_user['messages']  =   'Registration Successful';
        return response()->json($data_user, 200);
    }
}
