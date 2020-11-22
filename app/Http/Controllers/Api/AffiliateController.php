<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
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


    }
}
