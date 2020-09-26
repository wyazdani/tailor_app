<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\InformTailorJob;
use App\Jobs\OrderStatusJob;
use App\Models\Order;
use App\Models\Size;
use App\Traits\UploadImage;
use App\User;
use Illuminate\Http\Request;
use function GuzzleHttp\Psr7\str;

class OrderController extends Controller
{
    use UploadImage;

    public function index(Request $request)
    {
        $user   =   $request->user();
        $orders =   Order::from(get_table_name(Order::class).' as o')
            ->join(get_table_name(User::class).' as u','u.id','o.user_id')
            ->leftJoin(get_table_name(User::class).' as t','t.id','o.tailor_id')
            ->select('o.id as order_id','o.order_no','u.name as customer_name','t.name as tailor_name','o.order_status','o.created_at');
        if($user->role=='customer'){
            $orders =   $orders->where('u.id',$user->id);
        }elseif($user->role=='tailor'){
            $orders =   $orders->where('t.id',$user->id);
        }

        $orders =   $orders->paginate(20);
        if (!empty($orders)){
            foreach ($orders as $order)
            {
                $data['orders'][]   =   [
                    'order_id'  =>    $order->order_id,
                    'order_no'  =>    $order->order_no,
                    'customer_name'  =>    $order->customer_name,
                    'tailor_name'  =>    $order->tailor_name?$order->tailor_name:'Not Assigned Yet',
                    'order_status'  =>    $order->order_status,
                    'created_at'  =>    date('Y-m-d',strtotime($order->created_at)),
                ];

            }
            $data['links']['current_page'] = $orders->currentPage();
            $data['links']['first_page_url'] = $orders->url($orders->currentPage());
            $data['links']['from'] = $orders->firstItem();
            $data['links']['last_page'] = $orders->lastPage();
            $data['links']['last_page_url'] = $orders->url($orders->lastPage());
            $data['links']['next_page_url'] = $orders->nextPageUrl();
            $data['links']['per_page'] = $orders->perPage();
            $data['links']['prev_page_url'] = $orders->previousPageUrl();
            $data['links']['to'] = $orders->lastItem();
            $data['links']['total'] = $orders->total();
        }else{
            $data['orders'] =   [];
            $data['links'] = new \stdClass();
        }
        $data['status']  =   true;
        $data['messages']  =   'Orders Listing';
        return response()->json($data, 200);
    }
    public function store(Request $request)
    {
        $validation_fields  =   [
            'image_url'        => 'required|image|mimes:jpeg,png|max:8000',
            'size_name'        => 'required|max:255',
            'gender'        => 'required|in:male,female',
            'shoulder_to_seam'        => 'required|numeric',
            'shoulder_to_hips'        => 'required|numeric',
            'shoulder_to_floor'        => 'required|numeric',
            'arm_length'        => 'required|numeric',
            'bicep'        => 'required|numeric',
            'wrist'        => 'required|numeric',
            'waist'        => 'required|numeric',
            'lower_waist'        => 'required|numeric',
            'waist_to_floor'        => 'required|numeric',
            'hips'        => 'required|numeric',
            'max_thigh'        => 'required|numeric',
            'calf'        => 'required|numeric',
            'ankle'        => 'required|numeric',
            'chest'        => 'required|numeric',
            'navel_to_floor'        => 'required|numeric',
            'size_id'        => 'required_if:size_type,preset',
            'size_type'        => 'required|in:custom,preset',
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
        $image  =   $this->uploadImage($request->image_url);

        if ($request->size_id && $request->size_type=='preset'){
            $size   =   Size::find($request->size_id);
            $size->update([
                'name'        => $request->size_name,
                'gender'        => $request->gender,
                'shoulder_to_seam'        => $request->shoulder_to_seam,
                'shoulder_to_hips'        => $request->shoulder_to_hips,
                'shoulder_to_floor'        => $request->shoulder_to_floor,
                'arm_length'        => $request->arm_length,
                'bicep'        => $request->bicep,
                'wrist'        => $request->wrist,
                'waist'        => $request->waist,
                'lower_waist'        => $request->lower_waist,
                'waist_to_floor'        => $request->waist_to_floor,
                'hips'        => $request->hips,
                'max_thigh'        => $request->max_thigh,
                'calf'        => $request->calf,
                'ankle'        => $request->ankle,
                'chest'        => $request->chest,
                'navel_to_floor'        => $request->navel_to_floor,
            ]);
        }else{
            $size   =   Size::create([
                'user_id'        => $user->id,
                'name'        => $request->size_name,
                'gender'        => $request->gender,
                'shoulder_to_seam'        => $request->shoulder_to_seam,
                'shoulder_to_hips'        => $request->shoulder_to_hips,
                'shoulder_to_floor'        => $request->shoulder_to_floor,
                'arm_length'        => $request->arm_length,
                'bicep'        => $request->bicep,
                'wrist'        => $request->wrist,
                'waist'        => $request->waist,
                'lower_waist'        => $request->lower_waist,
                'waist_to_floor'        => $request->waist_to_floor,
                'hips'        => $request->hips,
                'max_thigh'        => $request->max_thigh,
                'calf'        => $request->calf,
                'ankle'        => $request->ankle,
                'chest'        => $request->chest,
                'navel_to_floor'        => $request->navel_to_floor,
            ]);
        }

        $order  =   Order::create([
             'order_no' =>  Order::CreateRandomBookingID(),
             'user_id' =>  $user->id,
             'tailor_id' =>  0,
             'size_id' =>  $size->id,
             'comments' =>  $request->comments,
             'image_url' =>  $image,
             'order_status' =>  'pending',
        ]);

        dispatch(new OrderStatusJob($user,$order))->delay(now()->addSeconds(30));
        $data_user['status']  =   true;
        $data_user['messages']  =   'Order Placed Successfully';
        return response()->json($data_user, 200);
    }
    public function edit(Request $request)
    {
        $validation_fields  =   [
            'order_id'         => 'required|exists:orders,id'
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

        $order  =   Order::from(get_table_name(Order::class).' as o')
            ->join(get_table_name(User::class).' as u','u.id','o.user_id')
            ->leftJoin(get_table_name(User::class).' as t','t.id','o.tailor_id')
            ->where('o.id',$request->order_id)
            ->select('o.id as order_id','u.name as customer_name','t.name as tailor_name','t.id as tailor_id','o.order_status')
            ->first();

        $tailors    =   User::where('role','tailor')->select('id as tailor_id','name as tailor_name')->get();


        $data   =   $order->toArray();
        $data['tailor_id']   =   $order->tailor_id?$order->tailor_id:0;
        $data['tailor_name']   =   $order->tailor_name?$order->tailor_name:'';
        $data['status']   =   true;
        $data['messages']   =   'Order edit';
        if (!empty($tailors)){
            $data['tailors']    = $tailors->toArray();
        }else{
            $data['tailors']    =   [];
        }
        return response()->json($data,200);
    }
    public function update(Request $request)
    {
        $validation_fields  =   [
            'order_id'         => 'required|exists:orders,id',
            'tailor_id'    => 'required|exists:users,id',
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

        $order  =   Order::find($request->order_id);

        $order->update([
            'tailor_id' =>  $request->tailor_id,
            'order_status'  =>  'processing'
        ]);

        $user   =   User::find($order->user_id);
        $tailor   =   User::find($order->tailor_id);
        dispatch(new OrderStatusJob($user,$order))->delay(now()->addSeconds(30));
        dispatch(new InformTailorJob($tailor,$order))->delay(now()->addSeconds(30));
        return response()->json([
            'status'     =>  true,
            'messages'   =>  'Order updated Successfully'
        ], 200);
    }

}
