<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','access_token','phone_number','address','role',
        'forget_code','confirmation_code','confirmed','is_active','affiliate_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function unique_code(){
        $unique_user    =  'AFF'.strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8));
        return $unique_user;
    }
    public static function CreateRandomAffiliateCode(){
        $self       =   new self();
        $code   =   $self->unique_code();
        $counts =   self::where('affiliate_code','=',$code)->count();
        if ($counts>0){
            return self::CreateRandomAffiliateCode();
        }else{
            return $code;
        }
    }
}
