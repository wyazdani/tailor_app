<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable =   ['user_id','type','credit','debit','balance','transaction_type','description'];
}
