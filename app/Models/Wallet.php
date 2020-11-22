<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable =   ['user_id','type','credit','debit','balance','transaction_type','description'];

    public function setBalanceAttribute(){
        $user_id        =   $this->user_id;
        $wallet         =   self::where('user_id', $user_id)->orderBy('id','DESC')->first();
        $total_balance  =   !empty($wallet)?$wallet->balance:0;
        if($this->transaction_type == 'credit'){
            $credit         =   $this->credit;
            $total_balance  +=  $credit;
        }
        else if($this->transaction_type == 'debit'){
            $debit          =   $this->debit;
            $total_balance  -=  $debit;
        }
        $this->attributes['balance']    =   (double)((isDecimal($total_balance))?number_format($total_balance,2):$total_balance);
    }
    public static function credit($user_id,$data = []){
        return self::create([
            'user_id'           =>  $user_id,
            'debit'             =>  0,
            'credit'            =>  $data['amount'],
            'type'              =>  $data['type'],
            'transaction_type'  =>  'credit',
            'description'       =>  !empty($data['description'])?$data['description']:'',
            'status'            =>  1,
            'balance'           =>  0,
        ]);
    }
    public static function debit($user_id,$data = []){
        return self::create([
            'user_id'           =>  $user_id,
            'debit'             =>  $data['amount'],
            'credit'            =>  0,
            'type'              =>  $data['type'],
            'transaction_type'  =>  'debit',
            'description'       =>  !empty($data['description'])?$data['description']:'',
            'status'            =>  1,
            'balance'           =>  0,
        ]);
    }
}
