<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\InformTailorJob;
use App\Jobs\OrderDeliveryDateAssigned;
use App\Jobs\OrderStatusJob;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Size;
use App\Models\Wallet;
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
            ->join(get_table_name(Size::class).' as s','s.id','o.size_id')
            ->leftJoin(get_table_name(User::class).' as t','t.id','o.tailor_id')
            ->select('o.id as order_id','o.order_no','u.name as customer_name','t.name as tailor_name','o.order_status','o.created_at','o.tailor_id','o.image_url','o.comments','o.delivery_date','u.phone_number','o.address','o.tracking_number','o.tailor_image','o.initial_remarks','o.complete_remarks','s.shoulder_to_seam','s.shoulder_to_hips','s.shoulder_to_floor','s.arm_length','s.bicep','s.wrist','s.waist','s.lower_waist','s.waist_to_floor',
                's.hips','s.max_thigh','s.calf','s.ankle','s.chest','s.navel_to_floor','s.name as size_name','s.gender','s.id as size_id','o.affiliate_code'
            )
            ->orderBy('o.id','DESC');
        if($user->role=='customer'){
            $orders =   $orders->where('u.id',$user->id);
        }elseif($user->role=='tailor'){
            $orders =   $orders->where('t.id',$user->id);
        }elseif ($user->role=='affiliate'){
            $orders =   $orders->where('o.affiliate_code',$user->affiliate_code);
        }

        $orders =   $orders->paginate(20);
        if (!empty($orders) && count($orders)>0){
            foreach ($orders as $order)
            {
                $data['orders'][]   =   [
                    'order_id'  =>    $order->order_id,
                    'size_id'  =>    $order->size_id,
                    'tailor_id'  =>    $order->tailor_id,
                    'order_no'  =>    $order->order_no,
                    'customer_name'  =>    $order->customer_name,
                    'customer_phone_number'  =>    $order->phone_number,
                    'customer_address'  =>    $order->address,
                    'tailor_name'  =>    $order->tailor_name?$order->tailor_name:'Not Assigned Yet',

                    'order_status'  =>    $order->order_status,
                    'created_at'  =>    date('Y-m-d',strtotime($order->created_at)),
                    'image_url'  =>    url($order->image_url),
                    'size_name'  =>    $order->size_name,
                    'gender'  =>    $order->gender,
                    'shoulder_to_seam'  =>    $order->shoulder_to_seam,
                    'shoulder_to_hips'  =>    $order->shoulder_to_hips,
                    'shoulder_to_floor'  =>    $order->shoulder_to_floor,
                    'arm_length'  =>    $order->arm_length,
                    'bicep'  =>    $order->bicep,
                    'wrist'  =>    $order->wrist,
                    'waist'  =>    $order->waist,
                    'lower_waist'  =>    $order->lower_waist,
                    'waist_to_floor'  =>    $order->waist_to_floor,
                    'hips'  =>    $order->hips,
                    'max_thigh'  =>    $order->max_thigh,
                    'calf'  =>    $order->calf,
                    'ankle'  =>    $order->ankle,
                    'chest'  =>    $order->chest,
                    'navel_to_floor'  =>    $order->navel_to_floor,
                    'comments'  =>    $order->comments,
                    'delivery_date'  =>    !empty($order->delivery_date)?date('M d Y',strtotime($order->delivery_date)):"",
                    'tracking_number'  =>    !empty($order->tracking_number)?$order->tracking_number:"",
                    'tailor_image'  =>    !empty($order->tailor_image)?url($order->tailor_image):"",
                    'initial_remarks'  =>    !empty($order->initial_remarks)?$order->initial_remarks:"",
                    'complete_remarks'  =>    !empty($order->complete_remarks)?$order->complete_remarks:"",
                    'affiliate_code'  =>    !empty($order->affiliate_code)?$order->affiliate_code:"",
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
            'image_url'        => 'required|image|mimes:jpeg,png|max:15000',
            'address'        => 'required',
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
            'affiliate_code'         => 'exists:users,affiliate_code'
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
        $image  =   '';
        if ($request->image_url){
            $image  =   $this->uploadImage($request->image_url);
        }


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
             'affiliate_code' =>  !empty($request->affiliate_code)?$request->affiliate_code:'',
             'comments' =>  $request->comments,
             'image_url' =>  $image,
             'order_status' =>  'pending',
             'address' =>  $request->address,

        ]);

        dispatch(new OrderStatusJob($user,$order))->delay(now()->addSeconds(30));
        $data_user['status']  =   true;
        $data_user['messages']  =   'Order Placed Successfully';
        return response()->json($data_user);
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
            'address'        => 'required',
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
            'size_id'        => 'required',
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

        $image  =   $order->image_url;
        if ($request->image_url){
            $this->deleteOne($order->image_url);
            $image = $this->uploadImage($request->image_url);
        }
        $order->update([
            'tailor_id' =>  $request->tailor_id,
            'order_status'  =>  'processing',
            'address'  =>  $request->address,
            'comments'  =>  $request->comments,
            'image_url'  =>  $image,
        ]);
        $size   =   Size::find($request->size_id);
        if (!empty($size)){
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
            Size::create([
                'user_id'        => $order->user_id,
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

        $user   =   User::find($order->user_id);
        $tailor   =   User::find($order->tailor_id);
        dispatch(new OrderStatusJob($user,$order))->delay(now()->addSeconds(30));
        if (!empty($tailor)){
            dispatch(new InformTailorJob($tailor,$order))->delay(now()->addSeconds(30));
        }
        return response()->json([
            'status'     =>  true,
            'messages'   =>  'Order updated Successfully'
        ], 200);
    }
    public function order_status(Request $request)
    {
        $validation_fields  =   [
            'order_status'         => 'required|in:completed,started',
            'delivery_date'         => 'required|date',
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

        $order  =   Order::find($request->order_id);

        $order_status   =   $request->order_status;
        $order->update([
            'order_status'  =>  $order_status,
            'delivery_date'  =>  date('Y-m-d',strtotime($request->delivery_date)),
            'initial_remarks'  =>  !empty($request->initial_remarks)?$request->initial_remarks:'',
        ]);

        $user   =   User::find($order->user_id);
        $manager    =   User::where('role','manager')->first();
        if ($order_status=='started'){
            dispatch(new OrderDeliveryDateAssigned($user,$order));
            dispatch(new OrderDeliveryDateAssigned($manager,$order));
        }else{
            dispatch(new OrderStatusJob($user,$order));
        }

        return response()->json([
            'status'     =>  true,
            'messages'   =>  'Order status updated Successfully'
        ], 200);
    }

    public function order_complete(Request $request)
    {
        $validation_fields  =   [
            'tailor_image'         => 'required|image|mimes:jpeg,png|max:15000',
            'tracking_number'         => 'required|max:255',
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

        $order  =   Order::find($request->order_id);
        $image  =   $this->uploadImage($request->tailor_image);
        $order->update([
            'order_status'  =>  'completed',
            'tailor_image'  =>  $image,
            'tracking_number'  =>  $request->tracking_number,
            'complete_remarks'  =>  !empty($request->complete_remarks)?$request->complete_remarks:'',
        ]);

        $user   =   User::find($order->user_id);
        $affiliate_user =   User::where('affiliate_code',$order->affiliate_code)->first();
        if ($affiliate_user){
            $affiliate_data   =   [
                'amount'            =>  Setting::find(1)->credit_affiliate,
                'type'              =>  'credit',
                'description'       =>  'Order No. '.$order->order_no. 'Completed Credits Received',
            ];
            Wallet::credit($affiliate_user->id,$affiliate_data);
        }
        $data   =   [
            'amount'            =>  Setting::find(1)->customer_point,
            'type'              =>  'point',
            'description'       =>  'Order No. '.$order->order_no. ' Completed Points Received',
        ];
        Wallet::credit($user->id,$data);
        dispatch(new OrderStatusJob($user,$order));

        return response()->json([
            'status'     =>  true,
            'messages'   =>  'Order Completed Successfully'
        ], 200);
    }

}
