<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\WithdrawRequestJob;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Wallet;
use App\User;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        $affiliates  =   User::where('role','affiliate');
        if ($request->search){
            $affiliates =   $affiliates->where('affiliate_code',$request->search);
        }
        $affiliates =   $affiliates->paginate(20);
        if (!empty($affiliates && count($affiliates)>0)){
            foreach ($affiliates as $customer)
            {
                $wallet =   Wallet::where('user_id',$customer->id)->orderBy('id','DESC')->first();
                $data['affiliates'][] =   [
                    'user_id'   =>  $customer->id,
                    'name'   =>  $customer->name,
                    'email'   =>  $customer->email,
                    'phone_number'   =>  $customer->phone_number?$customer->phone_number:'',
                    'address'   =>  $customer->address?$customer->address:'',
                    'affiliate_code'   =>  $customer->affiliate_code?$customer->affiliate_code:'',
                    'balance'   =>  !empty($wallet)?$wallet->balance:0,
                ];
            }
            $data['links']['current_page'] = $affiliates->currentPage();
            $data['links']['first_page_url'] = $affiliates->url($affiliates->currentPage());
            $data['links']['from'] = $affiliates->firstItem();
            $data['links']['last_page'] = $affiliates->lastPage();
            $data['links']['last_page_url'] = $affiliates->url($affiliates->lastPage());
            $data['links']['next_page_url'] = $affiliates->nextPageUrl();
            $data['links']['per_page'] = $affiliates->perPage();
            $data['links']['prev_page_url'] = $affiliates->previousPageUrl();
            $data['links']['to'] = $affiliates->lastItem();
            $data['links']['total'] = $affiliates->total();
        }else{
            $data['affiliates'] = [];
            $data['links'] = new \stdClass();
        }
        $data['status'] =   true;
        $data['messages'] =   'Affiliates Listing';
        return response()->json($data, 200);
    }

    public function withdrawRequest(Request $request)
    {
        $validation_fields  =   [
            'amount'           => 'required'

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


        $user   =   $request->user();
        $amount =   $request->amount;

        $this->dispatch(new WithdrawRequestJob($user,$amount));

        return response()->json([
            'status'     =>  true,
            'messages'   =>  'Withdraw request sent to Manager'
        ], 200);
    }

    public function deductAffiliateBalance(Request $request)
    {
        $validation_fields  =   [
            'amount'            =>  'required',
            'user_id'           =>  'required|exists:users,id'

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
        $user_id    =   $request->user_id;
        $amount    =   $request->amount;
        $wallet =   Wallet::where('user_id',$user_id)->orderBy('id','DESC')->first();
        if (!empty($wallet) && $wallet->balance>=$amount)
        {
            $data   =   [
                'amount'            =>  $amount,
                'type'              =>  'credit',
                'description'       =>  $amount.' Credits deducted by RoraProduction',
            ];
            Wallet::debit($user_id,$data);
        }else{
            return response()->json([
                'status'    =>  true,
                'messages'  =>  [
                    'Invalid Amount entered'
                ]
            ]);
        }
        return response()->json([
            'status'    =>  true,
            'messages'  =>  'Amount Deducted Successfully'
        ]);
    }
}
