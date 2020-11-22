<?php


namespace App\Utilities;


use App\User;

class UserHelper
{
    public static function userObject($id)
    {
        $user   =   User::find($id);

        $data['user_id']        =  $user->id;
        $data['name']           =  $user->name;
        $data['email']          =  $user->email;
        $data['access_token']   =  $user->access_token?$user->access_token:'';
        $data['phone_number']   =  $user->phone_number?$user->phone_number:'';
        $data['affiliate_code'] =  $user->affiliate_code?$user->affiliate_code:'';
        $data['address']        =  $user->address?$user->address:'';
        $data['role']           =  $user->role;
        return $data;
    }
}
