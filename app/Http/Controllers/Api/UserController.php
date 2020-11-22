<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function users(Request $request)
    {
        $validation_fields  =   [
            'role'     => 'required|in:customer,tailor',
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
        $customers  =   User::where('role',$request->role)->paginate(20);

        if (!empty($customers) && count($customers)>0){
            foreach ($customers as $customer)
            {
                $data['customers'][] =   [
                    'user_id'   =>  $customer->id,
                    'name'   =>  $customer->name,
                    'email'   =>  $customer->email,
                    'phone_number'   =>  $customer->phone_number?$customer->phone_number:'',
                    'address'   =>  $customer->address?$customer->address:'',
                ];
            }
            $data['links']['current_page'] = $customers->currentPage();
            $data['links']['first_page_url'] = $customers->url($customers->currentPage());
            $data['links']['from'] = $customers->firstItem();
            $data['links']['last_page'] = $customers->lastPage();
            $data['links']['last_page_url'] = $customers->url($customers->lastPage());
            $data['links']['next_page_url'] = $customers->nextPageUrl();
            $data['links']['per_page'] = $customers->perPage();
            $data['links']['prev_page_url'] = $customers->previousPageUrl();
            $data['links']['to'] = $customers->lastItem();
            $data['links']['total'] = $customers->total();
        }else{
            $data['customers'] = [];
            $data['links'] = new \stdClass();
        }
        $data['status'] =   true;
        $data['messages'] =   'Customers Listing';
        return response()->json($data, 200);

    }
}
