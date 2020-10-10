<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =   ['order_no','user_id','tailor_id','size_id','comments','image_url','order_status','address','delivery_date'];

    public function unique_code(){
        $unique_user    =  'ORD'.strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10));
        return $unique_user;
    }
    public static function CreateRandomBookingID(){
        $self       =   new self();
        $code   =   $self->unique_code();
        $counts =   self::where('order_no','=',$code)->count();
        if ($counts>0){
            return self::CreateRandomBookingID();
        }else{
            return $code;
        }
    }
}
