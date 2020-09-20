<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function create(Request $request)
    {
        $validation_fields  =   [
            'email'        => 'required|email',
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
        $user   =   User::where('email','=',$request['email'])->first();
        if ($user){
            $data   =   [
                'forget_code'   => mt_rand(100000, 999999),
            ];
            $us    =   $user;
            $us->update($data);
            Mail::send(new ResetPasswordMail($us));
            return response()->json([
                'status'     =>  true,
                'messages'   =>  'Reset password code has been sent to your email'
            ], 200);
        }
        else{
            return response()->json([
                'status'     =>  false,
                'messages'   =>  'Email not found'
            ], 200);
        }
    }

    public function store(Request $request)
    {
        $validation_fields  =   [
            'email'        => 'required|email',
            'code'         => 'required',
            'password'     => 'required|confirmed|string|min:6|max:90',
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
        $user   =   User::where('email','=',$request['email'])
            ->where('forget_code','=',$request['code'])
            ->first();
        if ($user){
            $data   =   [
                'password'          => Hash::make($request['password']),
                'forget_code'       =>  ''
            ];
            $user->update($data);
            return response()->json([
                'status'     =>  true,
                'messages'   =>  'Password changed successfully'
            ], 200);
        }
        else{
            return response()->json([
                'status'     =>  false,
                'messages'   =>  'Please enter valid code'
            ], 200);
        }
    }
}
