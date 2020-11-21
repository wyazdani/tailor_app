<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps  =   false;

    protected $fillable =   ['credit_affiliate','customer_point'];
}
