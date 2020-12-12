<?php


namespace App\Utilities;


use App\Models\Wallet;
use App\User;

class UserHelper
{
    public static function userObject($id)
    {
        $user   =   User::find($id);

        $wallet =   Wallet::where('user_id',$user->id)->orderBy('id','DESC')->first();
        $data['user_id']        =  $user->id;
        $data['name']           =  $user->name;
        $data['email']          =  $user->email;
        $data['access_token']   =  $user->access_token?$user->access_token:'';
        $data['phone_number']   =  $user->phone_number?$user->phone_number:'';
        $data['affiliate_code'] =  $user->affiliate_code?$user->affiliate_code:'';
        $data['address']        =  $user->address?$user->address:'';
        $data['role']           =  $user->role;
        $data['user_balance']   =  !empty($wallet)?$wallet->balance:0;
        return $data;
    }
}
