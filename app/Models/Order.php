<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =   ['order_no','user_id','tailor_id','size_id','comments','image_url','order_status'];
}
