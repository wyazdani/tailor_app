<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function myWallet(Request $request)
    {
        $user   =   $request->user();

        $wallets    =   Wallet::where('user_id',$user->id)
                        ->orderBy('id','DESC')
                        ->select('type','credit','debit','balance','transaction_type','description','created_at')
                        ->paginate(20);

        if (!empty($wallets && count($wallets)>0)){
            foreach ($wallets as $wallet)
            {
                $data['wallets'][] =   [
                    'type'              =>  $wallet->type,
                    'credit'            =>  $wallet->credit,
                    'debit'             =>  $wallet->debit,
                    'balance'           =>  $wallet->balance?$wallet->balance:0,
                    'transaction_type'  =>  $wallet->transaction_type,
                    'description'       =>  $wallet->description?$wallet->description:'',
                    'created_at'        =>  $wallet->created_at?date('Y-m-d',strtotime($wallet->created_at)):''
                ];
            }
            $data['links']['current_page'] = $wallets->currentPage();
            $data['links']['first_page_url'] = $wallets->url($wallets->currentPage());
            $data['links']['from'] = $wallets->firstItem();
            $data['links']['last_page'] = $wallets->lastPage();
            $data['links']['last_page_url'] = $wallets->url($wallets->lastPage());
            $data['links']['next_page_url'] = $wallets->nextPageUrl();
            $data['links']['per_page'] = $wallets->perPage();
            $data['links']['prev_page_url'] = $wallets->previousPageUrl();
            $data['links']['to'] = $wallets->lastItem();
            $data['links']['total'] = $wallets->total();
        }else{
            $data['wallets'] = [];
            $data['links'] = new \stdClass();
        }
        $data['status'] =   true;
        $data['messages'] =   'Wallet Transactions';
        return response()->json($data, 200);
    }
}
