<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            $this->dispatch(new ResetPasswordMail($us));
            return response()->json([
                'status'     =>  true,
                'messages'   =>  __('messages.code_send_to_email')
            ], 200);
        }
        else{
            return response()->json([
                'status'     =>  false,
                'messages'   =>  __('messages.email_not_found')
            ], 200);
        }
    }

    public function store(Request $request)
    {
        $validation_fields  =   [
            'email'        => 'required|email',
            'code'         => 'required',
            'password'      => ['required', 'string', 'min:6', 'confirmed','regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,120}$/'],
        ];
        $customMessages = [
            'regex' => __('messages.password_format')
        ];
        $validator     =  $this->getValidationFactory()->make($request->all(),$validation_fields,$customMessages);
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
                'messages'   =>  __('messages.password_changed_successfully')
            ], 200);
        }
        else{
            return response()->json([
                'status'     =>  false,
                'messages'   =>  __('messages.code_incorrect')
            ], 200);
        }
    }
}
